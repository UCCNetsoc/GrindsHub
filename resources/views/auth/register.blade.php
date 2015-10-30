@extends('layouts.default')

@yield('title')
Sign Up For GrindsHub
@stop

@yield('description')
GrindsHub is a place to find tutors and students. Unless you know a guy who knows a girl who knows an oracle, it can be hard to find someone who's doing grinds for your course. We make it easy, just start off with the searchbox and find tutors teaching what you want to know in your area. We're here to make it as easy as possible for you to learn what you need to learn.
@stop

@section('content')

<main class="row container">
	<section class="card-panel white col m6 offset-m3">
		<h3 class="center-align"> Register </h3>
		@foreach ($errors->all() as $message)
	        <li>{{ $message }}</li>
	    @endforeach

		{!! Form::open([
			"route" => ['user/store'],
			"method" => "POST",
			'class' => 'row col s12'
		]) !!}
		<div class="row">
			<div class="input-field">
				{!! Form::label('email', 'Email') !!}
				{!! Form::email('email', null, ["class" => "example"] ) !!}
			</div>
		</div>
		<div class="row">
			<div class="input-field">
				{!! Form::label('username', 'Username') !!}
				{!! Form::text('username', null, ["class" => "example"] ) !!}
			</div>
		</div>
		<div class="row">
			<div class="input-field col s6">
				{!! Form::label('first_name', 'First Name') !!}
				{!! Form::text('first_name', null) !!}
			</div>
			<div class="input-field col s6">
				{!! Form::label('last_name', 'Last Name') !!}
				{!! Form::text('last_name', null) !!}
			</div>
		</div>
		
		<div class="row">
			<div class="input-field">
				{!! Form::label('password', 'Password') !!}
				{!! Form::password('password', null, ["class" => "example"] ) !!}
			</div>
		</div>
		<div class="row">
			<div class="input-field">
				{!! Form::label('password_confirmation', 'Confirm Password') !!}
				{!! Form::password('password_confirmation', null, ["class" => "example"] ) !!}
			</div>
		</div>
		<button class="btn waves-effect waves-light" type="submit" name="action">Register
			<i class="mdi-content-send right"></i>
		</button>
		{!! Form::close() !!}

		<br /> 
	</section>

</main>

@endsection
