<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model {
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'messages';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['sender', 'receiver', 'status', 'message'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = [];

	public function scopeUnread($query, $user_id, $friend_id){
        
		return $query->where('status', '=', 'unread')
					 ->where('receiver', '=', $user_id)
					 ->where('sender', '=', $friend_id);
    }

    public function scopeLastMessage($query, $user_id, $friend_id){
        
		return $query->where('receiver', '=', $user_id)
					 ->where('sender', '=', $friend_id);
    }

    public static function getUnreadCount( $user_id ){
    	$messages = Message::where('status', '=', 'unread')
							 ->where('receiver', '=', $user_id)
							 ->get( );

		return count( $messages );
    }

}
