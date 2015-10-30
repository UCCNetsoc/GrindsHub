@extends('layouts.default')


@section('title')
{{ $user->first_name }} on GrindsHub
@stop

@section('extra-head')
<meta property="og:type" content="profile" />
<meta property="og:title" content="{{ $user->first_name . ' ' . $user->last_name }} - {{ $user->main_role }}" />
<meta property="og:url" content="{{ URL::route( 'user/profile', $user->username ) }}" />
<meta property="og:description" content="{{ $user->bio }}" />
<meta property="og:site_name" content="GrindsHub.com" />
<meta property="og:image" content="{{ URL::to('/') }}{{ $user->cover_picture }}" />
<meta property="og:locale" content="en_GB" />

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@thejokersthief">
<meta name="twitter:creator" content="@thejokersthief">
<meta name="twitter:title" content="{{ $user->first_name . ' ' . $user->last_name }} - {{ $user->main_role }}">
<meta name="twitter:description" content="{{ $user->bio }}">
<meta name="twitter:image" content="{{ URL::to('/') }}{{ $user->cover_picture }}">

@stop

@section('content')
	<style>
		.cover-photo:before{
			background: #000 url({{ $user->cover_picture or '/images/default/cover_photo' }}) no-repeat top center fixed;
			-webkit-background-size: cover;
			-moz-background-size: cover;
			-o-background-size: cover;
			background-size: cover;
			background-size: cover;
		}
	</style>
	<header class="cover-photo">
		
	</header>
		<section class="profile container row">

			<div class="m9 col">
				<div class="row">
					<figure class="m4 col profile-picture">
						<img src="{{ $user->picture or '/images/default/picture' }}" alt="">
						<figcaption class="sr-only">
							{{ $user->first_name .' '. $user->last_name }}
						</figcaption>
					</figure>

					<secion class="s12 m8 col">
						<h2>{{ $user->first_name .' '. $user->last_name }}</h2>
						<p>{{ $user->bio }}</p>

					</secion>
				</div>

				<div class="row subjects">
					@foreach( $user->subjects as $subject )
						<a href="#!"> {{ ucwords( trim( $subject ) ) }}</a>
					@endforeach
				</div>
			</div>

			<section class="s12 m3 col no-margin info">
				<aside class="space">
					<div class="ne"></div>
					<div class="nw primary">
						<p>{{ ucfirst( $user->main_role ) }}</p>
					</div>
				</aside>
				<aside class="space">
					<div class="ne"></div>
					<div class="nw primary">
						<p>{{ ucfirst( $user->county ) }}</p>
					</div>
				</aside>
				<aside class="space">
					<div class="ne"></div>
					<div class="nw primary">
						<p>€{{ $user->rate }}<small>/ hr</small></p>
					</div>
				</aside>

				<aside class="space">
					<div class="ne final"></div>
					<a class="waves-effect waves-light light-blue darken-2 nw primary btn contact modal-trigger" 

						href="@if( Auth::check( ) )
							#message-modal
						@else
							{{ URL::route( 'login' ) }}
						@endif">

						<p class="no-padding">
							Send Message
						</p>
					</a>
				</aside>
				
			</section>
		</section>
	
		<section class="container user-reviews">

			<section class="row">
				<div class="white col s3 m2 l1">
		          <span>
		          	<h3><i class="mdi-action-class"></i>{{ $user->count }}</h3>
		          </span>
		        </div>

		        <div class="white col s3 m2 l1">
		          <span>
		          	<h3><i class="mdi-action-grade"></i>{{ $user->average }}</h3>
		          </span>
		        </div>

		        <div class="white col s12 l5 m8 share offset-l5 valign-wrapper row">
		        	<h3 class="valign col m4 s7"> Share: &nbsp;</h3>
		          	<a class="col m2 s6" target="_BLANK" href="http://twitter.com/share?text={{ urlencode( 'Check out '.  $user->first_name .' '. $user->last_name . ' on GrindsHub.com' ) }}&url={{ Request::url( ) }}&hashtags=Grinds,GrindsHub">
		          		<i class="fa fa-twitter"></i>
		          	</a>
		          	<a class="col m2 s6" target="_BLANK" href="https://www.facebook.com/dialog/feed?app_id=374506336087647&display=popup&caption={{ urlencode( $user->first_name .' '. $user->last_name .' on GrindsHub.com' ) }}&link={{ Request::url() }}&redirect_uri={{ Request::url() }}">
		          		<i class="fa fa-facebook"></i>
		          	</a>
		          	<a class="col m2 s6" target="_BLANK" href="https://plus.google.com/share?url={{ Request::url( ) }}">
		          		<i class="fa fa-google-plus"></i>
		          	</a>
		          	<a class="col m2 s6" target="_BLANK" href="https://www.linkedin.com/shareArticle?mini=true&url={{ Request::url( ) }}&title={{ urlencode( 'Check out '.  $user->first_name .' '. $user->last_name . ' on GrindsHub.com' ) }}">
		          		<i class="fa fa-linkedin"></i>
		          	</a>
		          	<a class="col m2 s6" target="_BLANK" href="https://pinterest.com/pin/create/button/?url={{ Request::url( ) }}&media={{ URL::to( '/') . $user->cover_picture }}">
		          		<i class="fa fa-pinterest"></i>
		          	</a>
		        </div>
			</section>

			@foreach( $user->reviews as $review )
				<section class="row card-panel white">
					<figure class="m2 col picture">
						<img src="{{ $review->user->picture }}" alt="">
						<figcaption class="sr-only">
							{{ $review->user->first_name .' '. $review->user->last_name }}
						</figcaption>
					</figure>

					<section class="m7 col">
						<h2>{{ $review->user->first_name .' '. $review->user->last_name  }}</h2> 
						<h4>{{ $review->user->county }} - {{ $review->subject }}</h4>
						<p>{{ $review->text }}</p>
					</section>

					<section class="m3 col score">
						@for ($i = 0; $i < $review->wholeStars; $i++)
							<i class="fa fa-star"></i>
						@endfor

						@for ($i = 0; $i < $review->halfStars; $i++)
							<i class="fa fa-star-half-o"></i>
						@endfor

						@for ($i = 0; $i < $review->remainingStars; $i++)
							<i class="fa fa-star-o"></i>
						@endfor
					</section>
				</section>

			@endforeach

			<section class="row card-panel white review-form">
				@if(Auth::check( ))
					{!! Form::open( array( 'route' => array( 'add/review' ), 'method' => 'post', 'class' => 's12 m8 col offset-m2' ) ) !!}
						<h2> Submit A Review</h2>
						<br />

						<div class="row">
					        <div class="input-field col s12 m7">
					          <i class="mdi-action-assignment-ind prefix"></i>
					          <input id="subject" type="text" class="validate" name="subject">
					          <label for="subject">Taught me... (Subject)</label>
					        </div>
					        <div class="input-field col s12 m4 offset-m1">
					          	<p class="range-field rating">
					          		<i class="mdi-action-grade prefix"></i>
							      	<input type="range" min="0" max="5" step="0.5" value="3.5" onchange="showStars( )" name="rating"/>
							      	<div class="stars center-align">
					          			<i class="fa fa-star"></i>
					          			<i class="fa fa-star"></i>
					          			<i class="fa fa-star"></i>
					          			<i class="fa fa-star-half-o"></i>
					          			<i class="fa fa-star-o"></i>
					          		</div>
							    </p>
					        </div>
					    </div>

						<div class="input-field col s12">
					      <textarea id="textarea1" class="materialize-textarea" name="text"></textarea>
					      <label for="textarea1">My review:</label>
					    </div>
						
						<button class="btn waves-effect light-blue darken-2 waves-light" type="submit" name="action">Submit
							<i class="mdi-content-send right"></i>
						</button>

						{!! Form::hidden( 'id', $user->id ) !!}
					{!! Form::close( ) !!}
				@else
					<form class="s12 m8 col offset-m2">
						<h2> Submit A Review (must be logged in) </h2>
						<br />

						<div class="row">
					        <div class="input-field col s12 m7">
					          <i class="mdi-action-assignment-ind prefix"></i>
					          <input id="subject" type="text" class="validate" name="subject">
					          <label for="subject">Taught me... (Subject)</label>
					        </div>
					        <div class="input-field col s12 m4 offset-m1">
					          	<p class="range-field rating">
					          		<i class="mdi-action-grade prefix"></i>
							      	<input type="range" min="0" max="5" step="0.5" value="3.5" onchange="showStars( )" name="rating"/>
							      	<div class="stars center-align">
					          			<i class="fa fa-star"></i>
					          			<i class="fa fa-star"></i>
					          			<i class="fa fa-star"></i>
					          			<i class="fa fa-star-half-o"></i>
					          			<i class="fa fa-star-o"></i>
					          		</div>
							    </p>
					        </div>
					    </div>

						<div class="input-field col s12">
					      <textarea id="textarea1" class="materialize-textarea" name="text"></textarea>
					      <label for="textarea1">My review:</label>
					    </div>
						
						<button class="btn waves-effect light-blue darken-2 waves-light" type="submit" name="action">Submit
							<i class="mdi-content-send right"></i>
						</button>
					</form>
				@endif

			</section>
			<br />
		</section>
	</main>

	<div id="message-modal" class="modal">
		<div class="modal-content">
			{!! Form::open( array( 'route' => array( 'add/message', $user->username ), 'method' => 'post' ) ) !!}

				<div class="row">
					<div class="input-field col s12">
						<i class="mdi-action-announcement prefix hide-on-small-only"></i>
						{!! Form::textarea( 'message', null, [ 'class' => 'materialize-textarea', 'cols'=>'', 'rows'=>'', 'length' => 1000] ) !!}
						{!! Form::label( 'message', 'Message' ) !!}
					</div>
				</div>
				<button class="btn waves-effect waves-light" type="submit" name="action">
					Send
					<i class="mdi-content-send right"></i>
				</button>
				
				{{-- Use an encrypted user ID --}}
				{!! Form::hidden( 'receiver', $user->id ) !!}


				{!! Form::close( ) !!}
		</div>
	</div>

@stop