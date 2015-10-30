@extends('layouts.default')

@section('title')
Your Messages
@stop

@section('content')
	<main class="container row">
		@if( count( $contacts ) == 0 )
			<br />
			<div class="valign-wrapper" style="height: 100%;">
				<section class="col s12 center-align white-text valign">
					<h1>Nothing quite yet...</h1>
					<h1><i class="mdi-communication-chat" style="font-size: 5em; line-height: 1em"></i></h1>
					<h1>Why not message <a href="{{ URL::route('user/profile', 'thejokersthief') }}" class="black-text"> me?</a></h1>
				</section>
			</div>
		@endif

		<ul class="collection">
			@foreach( $contacts as $contact )
				<li class="collection-item avatar">
					<a href="{{ URL::route( 'thread/message', [ 'username' => $contact->username ] ) }}">
						<img src="{{ $contact['picture'] }}" alt="" class="circle">
					</a>
					<a class="dark-orange-text" href="{{ URL::route( 'thread/message', [ 'username' => $contact->username ] ) }}">
						<span class="title">{{ $contact['first_name'].' '.$contact['last_name'] }} ({{ $contact['username'] }})</span>
					</a>
					<p><em> {{ $contact['lastMessage']['message'] }}</em></p>
					<a href="#!" class="secondary-content"><i class="mdi-communication-message"></i></a>
				</li>
			@endforeach
		</ul>
	</main>
@stop