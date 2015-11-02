<?php namespace App\Http\Controllers;

use View;
use Auth;
use Response;
use Redirect;
use Request;
use Validator;
use Hash;
use Crypt;
use URL;
use Mail;
use App\User;
use App\Message;
use App\Friend;
use App\Invoice;	
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Stripe\Stripe;
use Stripe\Account;
use Stripe\Token;
use Stripe\Transfer;
use Stripe\Charge;
use Stripe\Error;
class PaymentController extends Controller
{	

	public function __construct( ){
		Stripe::setApiKey( env('STRIPE_API_SECRET') );
	}

	public function index( ){
		if (isset($_GET['code'])) { // Redirect w/ code
		  $code = $_GET['code'];

		  $token_request_body = array(
		    'grant_type' => 'authorization_code',
		    'client_id' => env('STRIPE_CLIENT_ID') ,
		    'code' => $code,
		    'client_secret' => env( 'STRIPE_API_SECRET' )
		  );

		  $req = curl_init('https://connect.stripe.com/oauth/token');
		  curl_setopt($req, CURLOPT_RETURNTRANSFER, true);
		  curl_setopt($req, CURLOPT_POST, true );
		  curl_setopt($req, CURLOPT_POSTFIELDS, http_build_query($token_request_body));

		  // TODO: Additional error handling
		  $respCode = curl_getinfo($req, CURLINFO_HTTP_CODE);
		  $resp = json_decode(curl_exec($req), true);
		  curl_close($req);

		  $user = User::find( Auth::user( )->id );
		  $user->access_token = Crypt::encrypt( $resp['access_token'] );

		  Stripe::setApiKey( $resp['access_token'] );
		  $account = Account::retrieve();
		  $user->recipient_id = $account['id'];
		  $user->save( );

		  return Redirect::route('payments');

		} else if (isset($_GET['error'])) { // Error
		  echo $_GET['error_description'];
		} 

		return View::make( 'payments.payments' );
	}


	public function addRecipient( ){
		// Update bank accounts at https://dashboard.stripe.com/account/transfers
		
		$user = User::find( Auth::user()->id );
		if( $user->recipient_id == null ){
			$data = Request::only([
						'country',
						'IBAN',
						'BIC',
						'currency'
					]);
			
			try{
				$token = Token::create([
				    'bank_account' => [
				        'country'        => $data['country'],
				        'routing_number' => $data['BIC'],		// IBAN
				        'account_number' => $data['IBAN'],
				        'currency'		 => $data['currency']
				    ],
				]);

				$account = Account::create(array(
					  "managed" => false,
					  "country" => $data['country'],
					  "email" => Auth::user( )->email,
					  "business_name" => $user->first_name .' '. $user->last_name,
					  "business_url" => URL::route( 'user/profile', [$user->username] ),
					  "default_currency" => $data['currency'],
					  "bank_account" => $token['id']
				));

				$user->recipient_id = $account['id'];
				$user->save( );
			} catch (Error\ApiConnection $e) {
			    // Network problem, perhaps try again.
				return Redirect::back( )->withErrors( ['message' => 'Network problem, perhaps try again. API Connection Error: ' .$e->getMessage( ) ] );
			} catch (Error\InvalidRequest $e) {
			    // You screwed up in your programming. Shouldn't happen!
				return Redirect::back( )->withErrors( ['message' => 'Invalid Request Error: ' .$e->getMessage( ) ] );
			} catch (Error\Api $e) {
			    // Stripe's servers are down!
				return Redirect::back( )->withErrors( ['message' => 'Stripe\'s servers are currently down :( Let me know at '.env("DEV_EMAIL") .'. Api Error: ' .$e->getMessage( ) ] );
			} catch (Error\Card $e) {
			    // Card was declined.
			    return Redirect::back( )->withErrors( ['message' => 'Card error: Your card was declined. '. $e->getMessage( ) ] );
			}
		}

		return Redirect::to( 'https://connect.stripe.com/oauth/authorize?response_type=code&client_id='. env('STRIPE_CLIENT_ID') .'&scope=read_write' );
	}

	public function addBankAccount( ){
		return View::make( 'payments.add_bank_account' )
					->with( 'countries', $this->countryCodes( ) )
					->with( 'currencies', $this->currencyCodes( ) );
	}

	public function invoices( ){
		return View::make( 'payments.invoices.invoices' )
					 ->with( 'invoices', $this->getInvoices( Auth::user( )->id ) );
	}

	public function getInvoices( $userId ){
		$invoices = Invoice::where('user_id', $userId)->get( );
		
		$index = 0;	
		foreach ($invoices as $invoice) {
			$user = User::find( $invoice->recipient_id );
			$invoices[$index]->recipient_username = $user->username . ' (' . $user->first_name . ')';
			$index++;
		}

		return $invoices;
	}

	public function showInvoice( $id ){
		$invoice = Invoice::find( Crypt::decrypt( $id ) );

		$invoice->id = Crypt::encrypt( $id );

		$invoice->sender = User::find( $invoice->user_id );
		$invoice->recipient = User:: find( $invoice->recipient_id );


		return View::make( 'payments.invoices.indiv-invoice' )->with( 'invoice', $invoice );
	}

	public function payInvoice( $id ){
		$invoice = Invoice::find( Crypt::decrypt( Crypt::decrypt( $id ) ) );
		$payRecipient = User::find( $invoice->user_id );
		$data = Request::only([
			'card_number',
			'CVC',
			'expiry_month',
			'expiry_year'
		]);

		$validator = Validator::make( $data, [
			'card_number' => 'required|min:10',
			'CVC' => 'required|size:3',
			'expiry_month' => 'required|integer',
			'expiry_year' => 'required|integer|min:2014'
		]);

		if( $validator->fails( ) ){
			return Redirect::back( )->withErrors( $validator );
		}

		try{
			$token = Token::create(array(
						  "card" => array(
						    "number" => $data['card_number'],
						    "exp_month" => $data['expiry_month'],
						    "exp_year" => $data['expiry_year'],
						    "cvc" => $data['CVC']
						  )
						));
			
			$applicationFee = ( 3.00 - (( $invoice->amount * 0.024 ) + 0.24) ) * 100;

		
			$charge = Charge::create(array(
						  "amount" => $invoice->amount * 100,
						  "currency" => "eur",
						  "source" => $token['id'], // obtained with Stripe.js
						  "description" => "Charge for " . $invoice->subject . " grind from GrindsHub.com",
						  "destination" => $payRecipient->recipient_id,
						  "application_fee" => $applicationFee
						));

			$invoice->paid = 'yes';
			$invoice->save( );

			return Redirect::route( 'thank-you/payments' );

		} catch (Error\ApiConnection $e) {
		    // Network problem, perhaps try again.
			return Redirect::back( )->withErrors( ['message' => 'Network problem, perhaps try again. API Connection Error: ' .$e->getMessage( ) ] );
		} catch (Error\InvalidRequest $e) {
		    // You screwed up in your programming. Shouldn't happen!
			return Redirect::back( )->withErrors( ['message' => 'I fucked up. Let me know at '.env("DEV_EMAIL") .'. Invalid Request Error: ' .$e->getMessage( ) ] );
		} catch (Error\Api $e) {
		    // Stripe's servers are down!
			return Redirect::back( )->withErrors( ['message' => 'Stripe\'s servers are currently down :( Let me know at '.env("DEV_EMAIL") .'. Api Error: ' .$e->getMessage( ) ] );
		} catch (Error\Card $e) {
		    // Card was declined.
		    return Redirect::back( )->withErrors( ['message' => 'Card error: Your card was declined. '. $e->getMessage( ) ] );
		}

	}

	public function thankYouMessage( ){
		return View::make('payments.invoices.thank-you');
	}

	public static function getOutstandingPayments( $userId ){
		return Invoice::where('recipient_id', $userId)
						->where('paid', 'no')
						->get( );
	}

	public function showOutstandingPayments( ){
		$invoices = $this->getOutstandingPayments( Auth::user( )->id );

		$index = 0;	
		foreach ($invoices as $invoice) {
			$user = User::find( $invoice->recipient_id );
			$invoices[$index]->recipient_username = $user->username . ' (' . $user->first_name . ')';
			$index++;
		}

		return View::make( 'payments.invoices.invoices' )->with( 'invoices', $invoices );
	}

	/**
	 * Request Invoice View
	 */
	public function requestInvoice( ){
		return View::make( 'payments.invoices.request' );
	}

	public function createInvoice( ){
		$data = Request::only( [
					'amount',
					'subject',
					'username'
				]);
		$validator = Validator::make($data, [
						'amount' => 'required|integer|min:0',
						'subject' => 'required',
						'username' => 'required|exists:users,username'
					]);

		if( $validator->fails( ) ){
			return Redirect::back( )->withInput( )->withErrors( $validator );
		}

		// The catch block is redundant security - the best kind of security
		try {
			$recipient = User::where( 'username', $data['username'] )->firstOrFail( );
		} catch (ModelNotFoundException $e) {
			return Redirect::back( )->withInput( )->withErrors( ['message' => 'That username doesn\'t exist. Check the spelling and try again' ]);
		}

		$data['recipient_id'] = $recipient->id;
		$data['user_id'] = Auth::user( )->id;
		$data['paid'] = 'no';
		$invoice = Invoice::create( $data );

		$recipient->url = URL::route( 'indiv-invoice/payments', [ $invoice->id ]);

		Mail::send('emails.request', ['recipient' => $recipient], function ($m) use ($recipient) {
            $m->to($recipient->email, $recipient->first_name)->subject( Auth::user( )->first_name . ' sent you a payment request on GrindsHub.com')->from( env('DEFAULT_EMAIL') );
        });


		return Redirect::route( 'invoices/payments' );
	}

	public function donation( ){
		return View::make( 'payments.donations.donation' );
	}

	public function donationThankYou( ){
		return View::make( 'payments.donations.thank-you' );
	}

	public function makeDonation( ){
		
		$data = Request::only([
			'card_number',
			'CVC',
			'expiry_month',
			'expiry_year',
			'amount'
		]);

		$validator = Validator::make( $data, [
			'card_number' => 'required|min:10',
			'CVC' => 'required|size:3',
			'expiry_month' => 'required|integer',
			'expiry_year' => 'required|integer|min:2014',
			'amount' => 'required|integer|min:1'
		]);

		if( $validator->fails( ) ){
			return Redirect::back( )->withErrors( $validator );
		}

		try{
			$token = Token::create(array(
						  "card" => array(
						    "number" => $data['card_number'],
						    "exp_month" => $data['expiry_month'],
						    "exp_year" => $data['expiry_year'],
						    "cvc" => $data['CVC']
						  )
						));
			
		
			$charge = Charge::create(array(
						  "amount" => $data['amount'] * 100,
						  "currency" => "eur",
						  "source" => $token['id'], 
						  "description" => "Donation to GrindsHub.com",
						));

			return Redirect::route( 'thank-you/donation' );

		} catch (Error\ApiConnection $e) {
		    // Network problem, perhaps try again.
			return Redirect::back( )->withErrors( ['message' => 'Network problem, perhaps try again. API Connection Error: ' .$e->getMessage( ) ] );
		} catch (Error\InvalidRequest $e) {
		    // You screwed up in your programming. Shouldn't happen!
			return Redirect::back( )->withErrors( ['message' => 'I fucked up. Let me know at '.env("DEV_EMAIL") .'. Invalid Request Error: ' .$e->getMessage( ) ] );
		} catch (Error\Api $e) {
		    // Stripe's servers are down!
			return Redirect::back( )->withErrors( ['message' => 'Stripe\'s servers are currently down :( Let me know at '.env("DEV_EMAIL") .'. Api Error: ' .$e->getMessage( ) ] );
		} catch (Error\Card $e) {
		    // Card was declined.
		    return Redirect::back( )->withErrors( ['message' => 'Card error: Your card was declined. '. $e->getMessage( ) ] );
		}
	}

	public static function countryCodes( ){
		return [ 	
			'AF' => 'Afghanistan',
		    'AX' => 'Aland Islands',
		    'AL' => 'Albania',
		    'DZ' => 'Algeria',
		    'AS' => 'American Samoa',
		    'AD' => 'Andorra',
		    'AO' => 'Angola',
		    'AI' => 'Anguilla',
		    'AQ' => 'Antarctica',
		    'AG' => 'Antigua and Barbuda',
		    'AR' => 'Argentina',
		    'AM' => 'Armenia',
		    'AW' => 'Aruba',
		    'AU' => 'Australia',
		    'AT' => 'Austria',
		    'AZ' => 'Azerbaijan',
		    'BS' => 'Bahamas the',
		    'BH' => 'Bahrain',
		    'BD' => 'Bangladesh',
		    'BB' => 'Barbados',
		    'BY' => 'Belarus',
		    'BE' => 'Belgium',
		    'BZ' => 'Belize',
		    'BJ' => 'Benin',
		    'BM' => 'Bermuda',
		    'BT' => 'Bhutan',
		    'BO' => 'Bolivia',
		    'BA' => 'Bosnia and Herzegovina',
		    'BW' => 'Botswana',
		    'BV' => 'Bouvet Island (Bouvetoya)',
		    'BR' => 'Brazil',
		    'IO' => 'British Indian Ocean Territory (Chagos Archipelago)',
		    'VG' => 'British Virgin Islands',
		    'BN' => 'Brunei Darussalam',
		    'BG' => 'Bulgaria',
		    'BF' => 'Burkina Faso',
		    'BI' => 'Burundi',
		    'KH' => 'Cambodia',
		    'CM' => 'Cameroon',
		    'CA' => 'Canada',
		    'CV' => 'Cape Verde',
		    'KY' => 'Cayman Islands',
		    'CF' => 'Central African Republic',
		    'TD' => 'Chad',
		    'CL' => 'Chile',
		    'CN' => 'China',
		    'CX' => 'Christmas Island',
		    'CC' => 'Cocos (Keeling) Islands',
		    'CO' => 'Colombia',
		    'KM' => 'Comoros the',
		    'CD' => 'Congo',
		    'CG' => 'Congo the',
		    'CK' => 'Cook Islands',
		    'CR' => 'Costa Rica',
		    'CI' => 'Cote d\'Ivoire',
		    'HR' => 'Croatia',
		    'CU' => 'Cuba',
		    'CY' => 'Cyprus',
		    'CZ' => 'Czech Republic',
		    'DK' => 'Denmark',
		    'DJ' => 'Djibouti',
		    'DM' => 'Dominica',
		    'DO' => 'Dominican Republic',
		    'EC' => 'Ecuador',
		    'EG' => 'Egypt',
		    'SV' => 'El Salvador',
		    'GQ' => 'Equatorial Guinea',
		    'ER' => 'Eritrea',
		    'EE' => 'Estonia',
		    'ET' => 'Ethiopia',
		    'FO' => 'Faroe Islands',
		    'FK' => 'Falkland Islands (Malvinas)',
		    'FJ' => 'Fiji the Fiji Islands',
		    'FI' => 'Finland',
		    'FR' => 'France, French Republic',
		    'GF' => 'French Guiana',
		    'PF' => 'French Polynesia',
		    'TF' => 'French Southern Territories',
		    'GA' => 'Gabon',
		    'GM' => 'Gambia the',
		    'GE' => 'Georgia',
		    'DE' => 'Germany',
		    'GH' => 'Ghana',
		    'GI' => 'Gibraltar',
		    'GR' => 'Greece',
		    'GL' => 'Greenland',
		    'GD' => 'Grenada',
		    'GP' => 'Guadeloupe',
		    'GU' => 'Guam',
		    'GT' => 'Guatemala',
		    'GG' => 'Guernsey',
		    'GN' => 'Guinea',
		    'GW' => 'Guinea-Bissau',
		    'GY' => 'Guyana',
		    'HT' => 'Haiti',
		    'HM' => 'Heard Island and McDonald Islands',
		    'VA' => 'Holy See (Vatican City State)',
		    'HN' => 'Honduras',
		    'HK' => 'Hong Kong',
		    'HU' => 'Hungary',
		    'IS' => 'Iceland',
		    'IN' => 'India',
		    'ID' => 'Indonesia',
		    'IR' => 'Iran',
		    'IQ' => 'Iraq',
		    'IE' => 'Ireland',
		    'IM' => 'Isle of Man',
		    'IL' => 'Israel',
		    'IT' => 'Italy',
		    'JM' => 'Jamaica',
		    'JP' => 'Japan',
		    'JE' => 'Jersey',
		    'JO' => 'Jordan',
		    'KZ' => 'Kazakhstan',
		    'KE' => 'Kenya',
		    'KI' => 'Kiribati',
		    'KP' => 'Korea',
		    'KR' => 'Korea',
		    'KW' => 'Kuwait',
		    'KG' => 'Kyrgyz Republic',
		    'LA' => 'Lao',
		    'LV' => 'Latvia',
		    'LB' => 'Lebanon',
		    'LS' => 'Lesotho',
		    'LR' => 'Liberia',
		    'LY' => 'Libyan Arab Jamahiriya',
		    'LI' => 'Liechtenstein',
		    'LT' => 'Lithuania',
		    'LU' => 'Luxembourg',
		    'MO' => 'Macao',
		    'MK' => 'Macedonia',
		    'MG' => 'Madagascar',
		    'MW' => 'Malawi',
		    'MY' => 'Malaysia',
		    'MV' => 'Maldives',
		    'ML' => 'Mali',
		    'MT' => 'Malta',
		    'MH' => 'Marshall Islands',
		    'MQ' => 'Martinique',
		    'MR' => 'Mauritania',
		    'MU' => 'Mauritius',
		    'YT' => 'Mayotte',
		    'MX' => 'Mexico',
		    'FM' => 'Micronesia',
		    'MD' => 'Moldova',
		    'MC' => 'Monaco',
		    'MN' => 'Mongolia',
		    'ME' => 'Montenegro',
		    'MS' => 'Montserrat',
		    'MA' => 'Morocco',
		    'MZ' => 'Mozambique',
		    'MM' => 'Myanmar',
		    'NA' => 'Namibia',
		    'NR' => 'Nauru',
		    'NP' => 'Nepal',
		    'AN' => 'Netherlands Antilles',
		    'NL' => 'Netherlands the',
		    'NC' => 'New Caledonia',
		    'NZ' => 'New Zealand',
		    'NI' => 'Nicaragua',
		    'NE' => 'Niger',
		    'NG' => 'Nigeria',
		    'NU' => 'Niue',
		    'NF' => 'Norfolk Island',
		    'MP' => 'Northern Mariana Islands',
		    'NO' => 'Norway',
		    'OM' => 'Oman',
		    'PK' => 'Pakistan',
		    'PW' => 'Palau',
		    'PS' => 'Palestinian Territory',
		    'PA' => 'Panama',
		    'PG' => 'Papua New Guinea',
		    'PY' => 'Paraguay',
		    'PE' => 'Peru',
		    'PH' => 'Philippines',
		    'PN' => 'Pitcairn Islands',
		    'PL' => 'Poland',
		    'PT' => 'Portugal, Portuguese Republic',
		    'PR' => 'Puerto Rico',
		    'QA' => 'Qatar',
		    'RE' => 'Reunion',
		    'RO' => 'Romania',
		    'RU' => 'Russian Federation',
		    'RW' => 'Rwanda',
		    'BL' => 'Saint Barthelemy',
		    'SH' => 'Saint Helena',
		    'KN' => 'Saint Kitts and Nevis',
		    'LC' => 'Saint Lucia',
		    'MF' => 'Saint Martin',
		    'PM' => 'Saint Pierre and Miquelon',
		    'VC' => 'Saint Vincent and the Grenadines',
		    'WS' => 'Samoa',
		    'SM' => 'San Marino',
		    'ST' => 'Sao Tome and Principe',
		    'SA' => 'Saudi Arabia',
		    'SN' => 'Senegal',
		    'RS' => 'Serbia',
		    'SC' => 'Seychelles',
		    'SL' => 'Sierra Leone',
		    'SG' => 'Singapore',
		    'SK' => 'Slovakia (Slovak Republic)',
		    'SI' => 'Slovenia',
		    'SB' => 'Solomon Islands',
		    'SO' => 'Somalia, Somali Republic',
		    'ZA' => 'South Africa',
		    'GS' => 'South Georgia and the South Sandwich Islands',
		    'ES' => 'Spain',
		    'LK' => 'Sri Lanka',
		    'SD' => 'Sudan',
		    'SR' => 'Suriname',
		    'SJ' => 'Svalbard & Jan Mayen Islands',
		    'SZ' => 'Swaziland',
		    'SE' => 'Sweden',
		    'CH' => 'Switzerland, Swiss Confederation',
		    'SY' => 'Syrian Arab Republic',
		    'TW' => 'Taiwan',
		    'TJ' => 'Tajikistan',
		    'TZ' => 'Tanzania',
		    'TH' => 'Thailand',
		    'TL' => 'Timor-Leste',
		    'TG' => 'Togo',
		    'TK' => 'Tokelau',
		    'TO' => 'Tonga',
		    'TT' => 'Trinidad and Tobago',
		    'TN' => 'Tunisia',
		    'TR' => 'Turkey',
		    'TM' => 'Turkmenistan',
		    'TC' => 'Turks and Caicos Islands',
		    'TV' => 'Tuvalu',
		    'UG' => 'Uganda',
		    'UA' => 'Ukraine',
		    'AE' => 'United Arab Emirates',
		    'GB' => 'United Kingdom',
		    'US' => 'United States of America',
		    'UM' => 'United States Minor Outlying Islands',
		    'VI' => 'United States Virgin Islands',
		    'UY' => 'Uruguay, Eastern Republic of',
		    'UZ' => 'Uzbekistan',
		    'VU' => 'Vanuatu',
		    'VE' => 'Venezuela',
		    'VN' => 'Vietnam',
		    'WF' => 'Wallis and Futuna',
		    'EH' => 'Western Sahara',
		    'YE' => 'Yemen',
		    'ZM' => 'Zambia',
		    'ZW' => 'Zimbabwe',
		];
	}

	public static function currencyCodes( ){
		return [
			'AED' => 	'United Arab Emirates Dirham',
			'AFN' =>	'Afghanistan Afghani',
			'ALL' =>	'Albania Lek',
			'AMD' =>	'Armenia Dram',
			'ANG' =>	'Netherlands Antilles Guilder',
			'AOA' =>	'Angola Kwanza',
			'ARS' =>	'Argentina Peso',
			'AUD' =>	'Australia Dollar',
			'AWG' =>	'Aruba Guilder',
			'AZN' =>	'Azerbaijan New Manat',
			'BAM' =>	'Bosnia and Herzegovina Convertible Marka',
			'BBD' =>	'Barbados Dollar',
			'BDT' =>	'Bangladesh Taka',
			'BGN' =>	'Bulgaria Lev',
			'BHD' =>	'Bahrain Dinar',
			'BIF' =>	'Burundi Franc',
			'BMD' =>	'Bermuda Dollar',
			'BND' =>	'Brunei Darussalam Dollar',
			'BOB' =>	'Bolivia Boliviano',
			'BRL' =>	'Brazil Real',
			'BSD' =>	'Bahamas Dollar',
			'BTN' =>	'Bhutan Ngultrum',
			'BWP' =>	'Botswana Pula',
			'BYR' =>	'Belarus Ruble',
			'BZD' =>	'Belize Dollar',
			'CAD' =>	'Canada Dollar',
			'CDF' =>	'Congo/Kinshasa Franc',
			'CHF' =>	'Switzerland Franc',
			'CLP' =>	'Chile Peso',
			'CNY' =>	'China Yuan Renminbi',
			'COP' =>	'Colombia Peso',
			'CRC' =>	'Costa Rica Colon',
			'CUC' =>	'Cuba Convertible Peso',
			'CUP' =>	'Cuba Peso',
			'CVE' =>	'Cape Verde Escudo',
			'CZK' =>	'Czech Republic Koruna',
			'DJF' =>	'Djibouti Franc',
			'DKK' =>	'Denmark Krone',
			'DOP' =>	'Dominican Republic Peso',
			'DZD' =>	'Algeria Dinar',
			'EGP' =>	'Egypt Pound',
			'ERN' =>	'Eritrea Nakfa',
			'ETB' =>	'Ethiopia Birr',
			'EUR' =>	'Euro',
			'FJD' =>	'Fiji Dollar',
			'FKP' =>	'Falkland Islands (Malvinas) Pound',
			'GBP' =>	'United Kingdom Pound',
			'GEL' =>	'Georgia Lari',
			'GGP' =>	'Guernsey Pound',
			'GHS' =>	'Ghana Cedi',
			'GIP' =>	'Gibraltar Pound',
			'GMD' =>	'Gambia Dalasi',
			'GNF' =>	'Guinea Franc',
			'GTQ' =>	'Guatemala Quetzal',
			'GYD' =>	'Guyana Dollar',
			'HKD' =>	'Hong Kong Dollar',
			'HNL' =>	'Honduras Lempira',
			'HRK' =>	'Croatia Kuna',
			'HTG' =>	'Haiti Gourde',
			'HUF' =>	'Hungary Forint',
			'IDR' =>	'Indonesia Rupiah',
			'ILS' =>	'Israel Shekel',
			'IMP' =>	'Isle of Man Pound',
			'INR' =>	'India Rupee',
			'IQD' =>	'Iraq Dinar',
			'IRR' =>	'Iran Rial',
			'ISK' =>	'Iceland Krona',
			'JEP' =>	'Jersey Pound',
			'JMD' =>	'Jamaica Dollar',
			'JOD' =>	'Jordan Dinar',
			'JPY' =>	'Japan Yen',
			'KES' =>	'Kenya Shilling',
			'KGS' =>	'Kyrgyzstan Som',
			'KHR' =>	'Cambodia Riel',
			'KMF' =>	'Comoros Franc',
			'KPW' =>	'Korea (North) Won',
			'KRW' =>	'Korea (South) Won',
			'KWD' =>	'Kuwait Dinar',
			'KYD' =>	'Cayman Islands Dollar',
			'KZT' =>	'Kazakhstan Tenge',
			'LAK' =>	'Laos Kip',
			'LBP' =>	'Lebanon Pound',
			'LKR' =>	'Sri Lanka Rupee',
			'LRD' =>	'Liberia Dollar',
			'LSL' =>	'Lesotho Loti',
			'LYD' =>	'Libya Dinar',
			'MAD' =>	'Morocco Dirham',
			'MDL' =>	'Moldova Leu',
			'MGA' =>	'Madagascar Ariary',
			'MKD' =>	'Macedonia Denar',
			'MMK' =>	'Myanmar (Burma) Kyat',
			'MNT' =>	'Mongolia Tughrik',
			'MOP' =>	'Macau Pataca',
			'MRO' =>	'Mauritania Ouguiya',
			'MUR' =>	'Mauritius Rupee',
			'MVR' =>	'Maldives (Maldive Islands) Rufiyaa',
			'MWK' =>	'Malawi Kwacha',
			'MXN' =>	'Mexico Peso',
			'MYR' =>	'Malaysia Ringgit',
			'MZN' =>	'Mozambique Metical',
			'NAD' =>	'Namibia Dollar',
			'NGN' =>	'Nigeria Naira',
			'NIO' =>	'Nicaragua Cordoba',
			'NOK' =>	'Norway Krone',
			'NPR' =>	'Nepal Rupee',
			'NZD' =>	'New Zealand Dollar',
			'OMR' =>	'Oman Rial',
			'PAB' =>	'Panama Balboa',
			'PEN' =>	'Peru Nuevo Sol',
			'PGK' =>	'Papua New Guinea Kina',
			'PHP' =>	'Philippines Peso',
			'PKR' =>	'Pakistan Rupee',
			'PLN' =>	'Poland Zloty',
			'PYG' =>	'Paraguay Guarani',
			'QAR' =>	'Qatar Riyal',
			'RON' =>	'Romania New Leu',
			'RSD' =>	'Serbia Dinar',
			'RUB' =>	'Russia Ruble',
			'RWF' =>	'Rwanda Franc',
			'SAR' =>	'Saudi Arabia Riyal',
			'SBD' =>	'Solomon Islands Dollar',
			'SCR' =>	'Seychelles Rupee',
			'SDG' =>	'Sudan Pound',
			'SEK' =>	'Sweden Krona',
			'SGD' =>	'Singapore Dollar',
			'SHP' =>	'Saint Helena Pound',
			'SLL' =>	'Sierra Leone Leone',
			'SOS' =>	'Somalia Shilling',
			'SPL' =>    'Seborga Luigino',
			'SRD' =>	'Suriname Dollar',
			'STD' =>	'São Tomé and Príncipe Dobra',
			'SVC' =>	'El Salvador Colon',
			'SYP' =>	'Syria Pound',
			'SZL' =>	'Swaziland Lilangeni',
			'THB' =>	'Thailand Baht',
			'TJS' =>	'Tajikistan Somoni',
			'TMT' =>	'Turkmenistan Manat',
			'TND' =>	'Tunisia Dinar',
			'TOP' =>	'Tonga Pa\'anga',
			'TRY' =>	'Turkey Lira',
			'TTD' =>	'Trinidad and Tobago Dollar',
			'TVD' =>	'Tuvalu Dollar',
			'TWD' =>	'Taiwan New Dollar',
			'TZS' =>	'Tanzania Shilling',
			'UAH' =>	'Ukraine Hryvnia',
			'UGX' =>	'Uganda Shilling',
			'USD' =>	'United States Dollar',
			'UYU' =>	'Uruguay Peso',
			'UZS' =>	'Uzbekistan Som',
			'VEF' =>	'Venezuela Bolivar',
			'VND' =>	'Viet Nam Dong',
			'VUV' =>	'Vanuatu Vatu',
			'WST' =>	'Samoa Tala',
			'XAF' =>	'Communauté Financière Africaine (BEAC) CFA Franc BEAC',
			'XCD' =>	'East Caribbean Dollar',
			'XDR' =>	'International Monetary Fund (IMF) Special Drawing Rights',
			'XOF' =>	'Communauté Financière Africaine (BCEAO) Franc',
			'XPF' =>	'Comptoirs Français du Pacifique (CFP) Franc',
			'YER' =>	'Yemen Rial',
			'ZAR' =>	'South Africa Rand',
			'ZMW' =>	'Zambia Kwacha',
			'ZWD' =>	'Zimbabwe Dollar'
		];
	}

}