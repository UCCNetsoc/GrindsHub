@extends('layouts.default')

@section('title')
Search
@stop

@section('extra-css')
	<meta property="og:type" content="website" />
	<meta property="og:title" content="GrindsHub.com - Learning Isn't A Spectator Sport" />
	<meta property="og:url" content="{{ URL::to('/') }}" />
	<meta property="og:description" content="GrindsHub lets students find, talk to and pay tutors in one easy place. Sign up as a teacher to let us handle all the hard stuff and let you focus on advertising your skills." />
	<meta property="og:site_name" content="GrindsHub.com" />
	<meta property="og:image" content="{{ URL::to('/') }}/images/og-image.png" />
	<meta property="og:locale" content="en_GB" />
@stop




@section('content')

<main class="container row search">
	<br />
	<nav class="dark-orange">
		<div class="nav-wrapper">
			{!! Form::open( ['route' => 'searchTranslation', 'method' => 'post'] ) !!}
				<div class="input-field">
					<input id="search" type="search" name="query" required class="tooltipped" data-position="bottom" data-delay="50" data-tooltip="Type in the subject you're looking for and the county you're in for the best results. EG:  Computer Science Cork" placeholder="Press enter to search...">
					<label for="search"><i class="mdi-action-search"></i></label>
					<i class="mdi-navigation-close"></i>
				</div>
			{!! Form::close( ) !!}
		</div>
	</nav>
	
	
	
	<div class="row">

		@if(isset($ishome))
			<div class="col m8 offset-m2">
				<img src="/images/featured-heading.png" id="featured-heading" alt="Featured Teachers"/>
			</div>
		@endif

		@foreach( $users as $user )
			@if( ($user->row_index % 3 == 0) && $user->row_index != 0 )
				</div>
				<div class="row">
			@endif

			<div class="col s12 m4">
		    	<div class="card">
		            <div class="card-image">
		              <a href="{{ URL::route( 'user/profile', $user->username ) }}">
		              	<img src="{{ $user->picture or '/images/default/profile_picture' }}">
		              </a>
		              <span class="card-title">{{ $user->first_name . ' ' . $user->last_name }}</span>
		            </div>
		            <div class="card-content">
		              <p>{{ $user->bio }}</p>
		            </div>
		            <div class="card-action center">
		              <a href="{{ URL::route( 'user/profile', $user->username ) }}" class="white-text waves-effect light-blue darken-2 waves-light btn"><i class="mdi-action-account-circle prefix"></i> View Profile</a>
		            </div>
		    	</div>
		    </div>
		@endforeach
	</div>
		
</main>

@stop