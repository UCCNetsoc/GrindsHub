<?php namespace App\Http\Controllers;

use View;
use App\Post;

class StaticController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders your application's "dashboard" for users that
	| are authenticated. Of course, you are free to change or remove the
	| controller as you wish. It is just here to get your app started!
	|
	*/
	public function renderPost( $slug ){
		$post = Post::where('slug', $slug)
					->first();



		$testoa = explode("\n", $post->text);
		//escape each line 
		foreach($testoa as $k=>$testo){
		    $testoa[$k] = $testo;
		}
		//implode each line in an array
		$post->text = "<p>".implode("</p>\n<p>", $testoa)."</p>\n";

		return View::make( 'static.post' )->with( 'post', $post );
	}

}
