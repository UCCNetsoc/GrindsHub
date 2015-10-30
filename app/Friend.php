<?php namespace App;

use Illuminate\Database\Eloquent\Model;
class Friend extends Model{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'friends';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['user_id','friend_id'];

	public static function get_friends( $user_id ){
		$friends = Friends::where('user_id', '=', $user_Id)->get();
		return $friends;
	}

}
