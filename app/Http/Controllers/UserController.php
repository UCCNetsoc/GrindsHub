<?php namespace App\Http\Controllers;

use View;
use Auth;
use Response;
use Redirect;
use Request;
use Validator;
use Hash;
use Image;
use App\User;
use App\Review;
use DB;
use Crypt;
use FilesystemIterator;
use URL;

// use Stripe;

class UserController extends Controller
{
	/*
	|--------------------------------------------------------------------------
	| Users Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders the "application" 
	|
	*/

	public function index( ){

		DB::statement('set @row_num = -1; ');
		$users = User::select( DB::raw( '*, 
			@row_num := @row_num + 1 as row_index, 
			IF( picture LIKE "%default%", 0, 1 ) as `order`' ) )
					 ->orderBy( 'order', 'desc' )
					 ->where('main_role', 'teacher')
					 ->where('bio', '<>', '')
					 ->take(12)
					 ->get( );
		


		return View::make( 'search.main' )->with( 'users', $users )->with( 'ishome' , true );
	}

	public function search( $query ){
		$query = str_replace('+', ' ', $query);
		DB::statement('set @row_num = -1; ');

		if( $found = $this->contains( $query, [ 'Antrim', 'Armagh', 'Carlow', 'Cavan', 'Clare', 'Cork', 'Derry', 'Donegal', 'Down', 'Dublin', 'Fermanagh', 'Galway', 'Kerry', 'Kildare', 'Kilkenny', 'Laois', 'Leitrim', 'Limerick', 'Longford', 'Louth', 'Mayo', 'Meath', 'Monaghan', 'Offaly', 'Roscommon', 'Sligo', 'Tipperary', 'Tyrone', 'Waterford', 'Westmeath', 'Wexford', 'Wicklow'])){

			$query = str_replace( $found, '', $query );
			$users = User::select( DB::raw( '*, @row_num := @row_num + 1 as row_index' ) )
					->whereRaw(
							"MATCH(subjects) AGAINST(? WITH QUERY EXPANSION) ", 
							array( trim( $query ) )
						)
					->where( 'county', '=', $found )
					->where('main_role', 'teacher')
					->get();
		} else {
			$users = User::select( DB::raw( '*, @row_num := @row_num + 1 as row_index' ) )
						->whereRaw(
							"MATCH(subjects) AGAINST(? WITH QUERY EXPANSION) ", 
							array( trim( $query ) )
						)
						->where('main_role', 'teacher')
						->get();

		}
		if(!isset($users[0])){
			$users = User::select( DB::raw( '*, @row_num := @row_num + 1 as row_index' ) )
						   ->where('subjects', 'LIKE', "%".trim($query)."%")
						   ->where('main_role', 'teacher')
						   ->get();

		}

		if(!isset($users[0])){
			$users = User::select( DB::raw( '*, @row_num := @row_num + 1 as row_index' ) )
						    ->whereRaw(
								"MATCH(bio) AGAINST(? WITH QUERY EXPANSION) ", 
								array( trim( $query ) )
							)
							->where('main_role', 'teacher')
						    ->get();

		}

		return View::make( 'search.main' )->with( 'users', $users );
	}

	public function contains($str, array $arr){
	    foreach($arr as $a) {
	        if (stripos($str,$a) !== false) return $a;
	    }
	    return false;
	}

	public function searchTranslation( ){
		$data = Request::only( ['query'] );

		$query = str_replace(' ', '+', $data['query'] );

		return Redirect::route( 'search', [ $query ] );
	}

	public function create(){
		return View::make('auth.register');
	}

	public function store( ){
		$data = Request::only( [
					'username',
					'password',
					'password_confirmation',
					'email',
					'first_name',
					'last_name'
				]);

		$validator = Validator::make( $data, [
					'username'  => 'required|unique:users|min:5|alpha_num',
					'email'     => 'email|required|unique:users',
					'password'  => 'required|confirmed|min:5',
					'first_name'=> 'required',
					'last_name' => 'required'
				]);

		if( $validator->fails( ) ){
			return Redirect::route( 'register' )
					->withErrors( $validator )
					->withInput( );
		}

		// Hash the password
		$data['password'] = Hash::make($data['password']);

		// Pick a default profile picture
			// Set out a base path and count number of files
			$path = storage_path().'/defaults/profile_picture';
			$fileIterator = new FilesystemIterator($path, FilesystemIterator::SKIP_DOTS);
			$fileCount = iterator_count($fileIterator);
			$files = glob($path . '/*.*');
			// Randomly pick a file from the directory
		    $randomIndex = array_rand( $files, 1);

			$data['picture'] = '/images/default/profile_picture/' . $randomIndex;

		// Pick a default cover picture
			// Set out a base path and count number of files
			$files = glob($path . '/*.*');
			$path = storage_path().'/defaults/cover_photo';
			$fileIterator = new FilesystemIterator($path, FilesystemIterator::SKIP_DOTS);
			$fileCount = iterator_count($fileIterator);

			// Randomly pick a file from the directory
		    $randomIndex = array_rand( $files, 1);

			$data['cover_picture'] = '/images/default/cover_photo/' . $randomIndex;

		$newUser = User::create( $data );

		if( $newUser ){
			// Login user
			Auth::login($newUser);
			return Redirect::route( 'user/update' );
		}


		return Redirect::route('register')->withInput();
	}

	public function login( ){
		if( Auth::check( ) ){
			return Redirect::to('/');
		}

		return View::make( 'auth.login' );
	}

	/**
     * Logs the current user out and redirects them to login
     */
    public function logout( ){
        if(Auth::check()){
          Auth::logout();
        }
        return Redirect::route('login');

    }

	public function update( ){
		return View::make( 'users.update' );
	}

	public function store_update( ){

		if( $_FILES['picture']['size'] > 2000000 || $_FILES['cover_picture']['size'] > 2000000 ){
			
			return Redirect::route( 'user/update' )
					->withErrors( array('message' => 'Images must be less than 2MB in size') )
					->withInput( );
		}

		$data = Request::only( [
					'first_name',
					'last_name',
					'bio',
					'picture',
					'cover_picture',
					'main_role',
					'county',
					'rate',
					'subjects',

					
				]);

		$validator = Validator::make( $data, [
					'first_name'	=> 'required',
					'last_name' 	=> 'required',
	                'picture' 		=> 'image|max:2048',
	                'cover_picture' => 'image|max:2048',
	                'main_role'		=> 'in:student,teacher',
					'rate'			=> 'numeric',
					'subjects'		=> 'max:150',
					'bio'			=> 'max:250'					
				]);

		if( $validator->fails( ) ){
			return Redirect::route( 'user/update' )
					->withErrors( $validator )
					->withInput( );
		}



		$data['picture'] = Auth::user( )->picture;
		$data['cover_picture'] = Auth::user( )->cover_picture;

		if (Request::hasFile('picture')){
			// Get the file from the request
            $file = Request::file('picture');

            $destination_path = storage_path() .'/uploads/';
            // Create a filename by hashing the user's username. This
            // will mean each user only has one profile picture residing
            // on our filesystem
            $file_name = hash('ripemd160', Auth::user()->username ) .'_picture.'. $file->getClientOriginalExtension();
            // Move the file to our server
            $movement = Request::file('picture')->move($destination_path, $file_name);

            // Perform an image intervention, getting best fit from image
            // and saving it again
            $image = Image::make( storage_path(). '/uploads/'. $file_name);
            $image->fit( 500, 500 );
            $image->save(storage_path(). '/uploads/'. $file_name);

            $data['picture'] = (string) "/images/". $file_name;
           
		}

		if (Request::hasFile('cover_picture')){
			// Get the file from the request
            $file = Request::file('cover_picture');
            $destination_path = storage_path() .'/uploads/';
            // Create a filename by hashing the user's username. This
            // will mean each user only has one profile picture residing
            // on our filesystem
            $file_name = hash('ripemd160', Auth::user()->username ) .'_cover_picture.'. $file->getClientOriginalExtension();
            // Move the file to our server
            $movement = Request::file('cover_picture')->move($destination_path, $file_name);

            // Perform an image intervention, getting best fit from image
            // and saving it again
            $image = Image::make( storage_path(). '/uploads/'. $file_name);
            $image->fit( 1200, 630 );
            $image->save(storage_path(). '/uploads/'. $file_name);

            $data['cover_picture'] = (string) "/images/". $file_name;
		}

		$user = User::find( Auth::user( )->id );
		$user->bio = $data['bio'];
		$user->update( $data );

		return Redirect::route('user/update');
	}

	public function handleLogin( ){
		$data = Request::only(['email', 'password']);

		$validator = Validator::make(
			$data,
			[
				'email' => 'required|email|min:8',
				'password' => 'required',
			]
		);

		if($validator->fails()){
			return Redirect::route('login')->withErrors($validator)->withInput();
		}

		if( Auth::attempt(['email' => $data['email'], 'password' => $data['password']], true) ){

			try{
				$user_details = User::where( 'email', '=', $data['email'] )->firstOrFail();
			} catch( ModelNotFoundException $e ){
				return Redirect::route('register')->withInput();
			}
			return Redirect::to('/');
		} else {
			return Redirect::route('login')->withErrors(['msg'=>'I\'m sorry, that username and password aren\'t correct.' ]);
		}

		return Redirect::route('login')->withInput();
	}

	/**
     * Render a profile view for a given username
     * @param  string $username The username of the user being viewed
     */
    public function profile( $username ){
        
        // Try to get the user, if it fails, throw a 404 error
        try{
            $user = User::where('username', '=', $username )->firstOrFail();
            $user->reviews = Review::getReviews( $user->id );
            $user->count = Review::getCount( $user->id );
            $user->average = Review::getAverageScore( $user->id );

            $index = 0;
            foreach ($user->reviews as $review) {
            	$user->reviews[$index]->user = User::find( $review->reviewer_id );

            	$score = $review->rating / 0.5;
            	$user->reviews[$index]->wholeStars = floor( $score / 2 );
            	$user->reviews[$index]->halfStars = $score % 2;

            	$totalStars = $user->reviews[$index]->wholeStars + ( $user->reviews[$index]->halfStars == 0 ? 0 : 1 );
            	$user->reviews[$index]->remainingStars = 5 - $totalStars;

 				$index++;
            }



            $user->id = Crypt::encrypt( $user->id );

            $user->subjects = explode( ',' , $user->subjects );

	        // Render the view
	        return View::make('users.profile')->with('user', $user);
        } catch( ModelNotFoundException $e ){
            abort( 404 );
        }
    }
}
