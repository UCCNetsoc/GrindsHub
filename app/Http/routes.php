<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'WelcomeController@index');

Route::get('home', 'UserController@index');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);

Route::get('/register', ['as' => 'register', 'uses' => 'UserController@create']);
Route::post('/user/store', ['as' => 'user/store', 'uses' => 'UserController@store']);

Route::get('/login', ['as' => 'login', 'uses' => 'UserController@login']);
Route::post('/user/login', ['as' => 'handleLogin', 'uses' => 'UserController@handleLogin']);


Route::post( '/search/for', ['as' => 'searchTranslation', 'uses' => 'UserController@searchTranslation']);
Route::get( '/search/{query}', ['as' => 'search', 'uses' => 'UserController@search' ] );

////////////
// STATIC //
////////////
Route::get('/blog/{slug}', ['as' => 'posts', 'uses' => 'StaticController@renderPost'] );

//////////
// USER //
//////////
Route::group( ['prefix' => 'user' ], function( ){ 
    Route::group( ['middleware' => 'auth'], function( ){
        Route::get('/update/user', ['as' => 'user/update', 'uses' => 'UserController@update']);
        Route::post('/update/user', ['as' => 'user/store_update', 'uses' => 'UserController@store_update']);
    });

    Route::get('{username}', ['as' => 'user/profile', 'uses' => 'UserController@profile' ] );
});

///////////////
// MESSAGES  //
///////////////
Route::group( ['prefix' => 'messages', 'middleware' => 'auth'], function( ){ 
    Route::post( '/add-friend/{ userId }', ['as' => 'add_friend', 'uses' => 'MessageController@add_friend'] );

    Route::get( '/', ['as' => 'all/message', 'uses' => 'MessageController@index' ] );
    Route::get( '/{username}/{json?}', ['as' => 'thread/message', 'uses' => 'MessageController@showMessageThread']);
});


/////////////
// REVIEWS //
/////////////
Route::group( ['prefix' => 'reviews', 'middleware' => 'auth'], function( ){ 
    Route::post( '/', ['as' => 'add/review', 'uses' => 'ReviewController@store'] );
    Route::post( '/update', ['as' => 'update/review', 'uses' => 'ReviewController@store_update'] );
});


//////////////
// PAYMENTS //
//////////////
Route::group( ['prefix' => 'payments', 'middleware' => 'auth' ], function( ){
    Route::get('/', ['as' => 'payments', 'uses' => 'PaymentController@index']);

    Route::get( '/invoices', ['as' => 'invoices/payments', 'uses' => 'PaymentController@invoices' ]);
    Route::get('/invoices/request', ['as' => 'request-invoice/payments', 'uses' => 'PaymentController@requestInvoice']);
    Route::post('/invoices/request', ['as' => 'create-invoice/payments', 'uses' => 'PaymentController@createInvoice']);
    Route::get('/invoices/outstanding', ['as' => 'outstanding/payments', 'uses' => 'PaymentController@showOutstandingPayments']);
    Route::get( '/invoices/{id}', ['as' => 'indiv-invoice/payments', 'uses' => 'PaymentController@showInvoice' ]);
    Route::post( '/invoices/pay/{id}', ['as' => 'pay-invoice/payments', 'uses' => 'PaymentController@payInvoice' ]);

    Route::get( '/add-bank-account', ['as' => 'add-bank/payments', 'uses' => 'PaymentController@addBankAccount']);
    Route::post( '/store-bank-account', ['as' => 'store-bank/payments', 'uses' => 'PaymentController@addRecipient']);

    Route::get( '/thank-you', ['as' => 'thank-you/payments', 'uses' => 'PaymentController@thankYouMessage']);
});


Route::group( ['prefix' => 'donation'], function( ){
    Route::get('/', ['as' => 'donation', 'uses' => 'PaymentController@donation']);
    Route::post('/', ['as' => 'make/donation', 'uses' => 'PaymentController@makeDonation'] );
    Route::get('/thank-you', ['as' => 'thank-you/donation', 'uses' => 'PaymentController@donationThankYou']);
});


/////////////////
// JSON ROUTES //
/////////////////

Route::group( ['prefix' => 'json', 'middleware' => 'auth'], function(){
    

    Route::group( ['prefix' => 'messages' ], function( ){
    	Route::post( '/add/{username}', ['as' => 'add/message', 'uses' => 'MessageController@store' ] );
    	Route::get( '/get/{user_id}/{friend_id}', [ 'as' => 'get/message', 'uses' => 'MessageController@getMessages' ] );
    });
});

// Define a custom translation for default images
// Acceptable "types": profile, cover_photo
Route::get('images/default/{type}/{index?}', function($type = 'profile_picture', $index = null)
{

    if( $type == 'profile_picture_alt' ){
        $type = 'profile_picture';
    }
	// Set out a base path and count number of files
	$path = storage_path().'/defaults/' . $type;
	$fileIterator = new FilesystemIterator($path, FilesystemIterator::SKIP_DOTS);
	$fileCount = iterator_count($fileIterator);

	// Randomly pick a file from the directory
	$files = glob($path . '/*.*');
    $randomIndex = ( $index == null ? array_rand( $files, 1) : $index );

    $path = $files[ $randomIndex ];

    if ( file_exists($path) ) {
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        return Response::download($path);
    }
});

// Getting files via a public url
Route::get('images/{image}', function($image = null)
{
    $path = storage_path().'/uploads/' . $image;
    if (file_exists($path)) { 
        return Response::download($path);
    }
});