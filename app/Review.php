<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Review extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'reviews';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['user_id', 'reviewer_id', 'subject', 'rating', 'text'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = [];

	public static function getReviews( $userId ){
		return Review::where( 'user_id', $userId )->get( );
	}

	public static function getAverageScore( $userId ){
		$results = Review::select( DB::raw('AVG( rating ) as average' ) )
							->where( 'user_id', $userId )->first( );

		return $results->average;
	}

	public static function getCount( $userId ){
		$results = Review::select( DB::raw('COUNT( * ) as count' ) )
							->where( 'user_id', $userId )->first( );

		return $results->count;
	}
}