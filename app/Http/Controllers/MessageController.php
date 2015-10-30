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
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mail;

class MessageController extends Controller
{


	/**
	 * Show all the friends of a user and unread messages
	 */
	public function index( ){
		$userId = Auth::user( )->id;

		$friends = $this->getFriends( $userId );

		$friendsInfo = array();
		foreach ($friends as $friendId ) {
			$user = User::find( $friendId );
	        $unreadMessages = Message::unread( $userId, $friendId )->get( );
	        $user->unreadCount = count( $unreadMessages );
	        $user->lastMessage = Message::lastMessage( $userId, $friendId )->first( );
	        $friendsInfo[] = $user;
		}

		return View::make( 'messages.static.all_messages' )->with( 'contacts', $friendsInfo );
	}

	/**
	 * Display the message thread between logged in user and a friend
	 * @param  integer $username The other person in the conversation
	 */
	public function showMessageThread( $username ){
		$userId = Auth::user( )->id;

		$user = User::where( 'username', $username )->first( );
		$friendId = $user->id;

		$senderInfo = User::find( $userId );

        // Mark all unread messages as read
		Message::unread( $senderInfo->id, $user->id )
		         ->update( array( 'status' => 'read' ) );

		$messages = json_decode( $this->getMessages( $userId, $friendId )->getContent( ) );

		if( empty( $messages ) ){
			$messages->message = array( );
		}

		if( $user->profile_picture == '' ){
			$user->profile_picture = '/images/default/profile_picture';
		}

		if( $senderInfo->profile_picture == '' ){
			$senderInfo->profile_picture = '/images/default/profile_picture_alt';
		}

		return View::make( 'messages.static.thread' )->with( [ 
								'messages' => $messages->message, 

								// Encrypt the userId to avoid tampering
								'userId' => Crypt::encrypt( $friendId ), 
								'friendInfo' => $user, 
								'senderInfo' => $senderInfo ] );
	}

	/**
	 * Creates a new message
	 * @return json Returns an error or success
	 */
	public function store( $username, $json = false ){

		$data = Request::only( [
					'sender',
					'receiver',
					'message',
				]);

		$data['sender'] = Auth::user( )->id;
		$data['receiver'] = Crypt::decrypt( $data['receiver'] );

		$validator = Validator::make( $data, [
					'sender' => 'required|numeric',
					'receiver' => 'required|numeric',
				]);

		if( $validator->fails( ) ){
			$jsonResponse = [
				'status' => 'error',
				'message' => 'Validation failed'
			];

			return Response::json( $jsonResponse );
		}

		try {
			$friendExists = Friend::where( 'user_id', '=', $data['sender'] )
		                           ->where( 'friend_id', '=', $data['receiver'] )
		                           ->firstOrFail( );

		    $currentUser = Friend::where( 'friend_id', '=', $data['sender'] )
		                           ->where( 'user_id', '=', $data['receiver'] )
		                           ->first( );

		    // arbitray update to use "updated_at" as a sorting method
		    $friendExists->touch( );
		    $currentUser->touch( );


		} catch (ModelNotFoundException $e) {
			// Add a friend when the first message is sent
			$friendDetails = [
				'user_id' 	=> $data['sender'],
				'friend_id' => $data['receiver']
			];
			Friend::create( $friendDetails );

			// Friends are a two-way relationship
			// Add the friend going the other way as well
			$friendDetails = [
				'friend_id' => $data['sender'],
				'user_id' 	=> $data['receiver']
			];
			Friend::create( $friendDetails );
		}

		$data['status'] = 'unread';
		$message = Message::create( $data );


		if( $message ){
			$recipient = User::find( $data['receiver'] );

			if( $recipient->notifications != 'no' ){
				Mail::send('emails.new_message', ['recipient' => $recipient], function ($m) use ($recipient) {
		            $m->to($recipient->email, $recipient->first_name)->subject( Auth::user( )->first_name . ' sent you a message on GrindsHub.com')->from( env('DEFAULT_EMAIL') );
		        });
			}
			
			$jsonResponse = [
				'status' => 'success',
				'message' => $data['message']
			];
			return ( $json ? 
						  Response::json( $jsonResponse ) 
						: Redirect::route('thread/message', ['username' => $username] ) );
		}

		$jsonResponse = [
			'status' => 'error',
			'message' => 'The message did not save correctly'
		];


		return Response::json( $jsonResponse );
	}

	/**
	 * Get all the messages between two users
	 * @param  integer $userId   
	 * @param  integer $friendId
	 * @return array   All the messages
	 */
	public function getMessages( $userId, $friendId ){

		try {
			$messages = Message::where( function( $query ) use( &$userId, &$friendId ){
									$query->where( 'sender', '=', $userId )
					                      ->where( 'receiver', '=', $friendId );
								})	
					            ->orWhere( function( $query ) use( &$userId, &$friendId ){ 
					            	$query->where( 'sender', '=', $friendId )
					            		  ->where( 'receiver', '=', $userId );
					            })
					            ->orderBy( 'created_at', 'desc' )
					            ->get( );
			$jsonResponse = [
				'status' => 'success',
				'message' => $messages
			];
			return Response::json( $jsonResponse );

		} catch (ModelNotFoundException $e) {
			$jsonResponse = [
				'status' => 'success',
				'message' => []
			];
			return Response::json( $jsonResponse );
		}
	}

	/**
	 * Get a certain user's friends
	 * @param  integer $user_id 
	 * @return array   IDs of the friends
	 */
	public function getFriends( $userId ){

		$friends = Friend::where('user_id', '=', $userId )
						 ->orderBy('updated_at', 'desc')
						 ->get( );

		if( $friends ){
			$friendIds = [];
			foreach ($friends as $friend) {
				$friendIds[] = $friend->friend_id;
			}

			return $friendIds;
		} else {
			return array( );
		}

	}

}