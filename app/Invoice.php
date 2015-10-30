<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class Invoice extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'invoices';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['recipient_id', 'user_id', 'subject', 'date', 'amount', 'paid'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = [];

	public static function getOutstandingPayments( $userId ){

		return Invoice::where('recipient_id', $userId)
						->where('paid', 'no')
						->get( );
	}
}
