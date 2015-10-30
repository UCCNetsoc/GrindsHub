<?php namespace App\Http\Controllers;

use View;
use Auth;
use Response;
use Redirect;
use Request;
use Validator;
use Hash;
use Crypt;
use App\User;
use App\Message;
use App\Token;
use App\Friend;
use App\Review;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ReviewController extends Controller
{
	public function store( ){
		$data = Request::only(['subject', 'rating', 'text', 'id']);

		$validator = Validator::make( $data, [
					'subject' => 'max: 100',
					'rating'  => 'numeric',
					'text'    => 'max: 400',
				]);

		$user = User::find( Crypt::decrypt( $data['id'] ) );

		if( $validator->fails( ) ){
			return Redirect::route( 'user/profile', [ $user->username ] )
					->withErrors( $validator )
					->withInput( );
		}

		$data['user_id'] = $user->id;
		$data['reviewer_id'] = Auth::user( )->id;

		Review::create( $data );

		return Redirect::route( 'user/profile', [ $user->username ] );

	}

	public function store_update( ){
		$data = Request::only(['subject', 'rating', 'text', 'id', 'review_id']);

		$validator = Validator::make( $data, [
					'subject' => 'max: 100',
					'rating'  => 'numeric',
					'text'    => 'max: 400',
				]);

		$user = User::find( Crypt::decrypt( $data['id'] ) );

		if( $validator->fails( ) ){
			return Redirect::route( 'user/profile', [ $user->username ] )
					->withErrors( $validator )
					->withInput( );
		}

		$review = Review::find( Crypt::decrypt( $data['review_id'] ) );

		$review->update( $data );

		return Redirect::route( 'user/profile', [ $user->username ] );
	}

}