@extends('layouts.default')

@yield('title')
Sign In To GrindsHub
@stop

@yield('description')
GrindsHub is a place to find tutors and students. Unless you know a guy who knows a girl who knows an oracle, it can be hard to find someone who's doing grinds for your course. We make it easy, just start off with the searchbox and find tutors teaching what you want to know in your area. We're here to make it as easy as possible for you to learn what you need to learn.
@stop


@section('content')
<main class="row container">
	<section class="card-panel white col m6 offset-m3">
		<h3 class="center-align"> Login </h3>
		@include('forms.login')

		<br />
	</section>

</main>
@endsection
