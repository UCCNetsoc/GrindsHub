@extends('layouts.default')


@section('content')
	
	<div class="container row">
		<main class="card-panel white col s12 m8 offset-m2 valign-wrapper" style="height: 400px">
			<h5 class="valign center-align">Thank you for donating! You stay awesome! Email me with any questions <a href="mailto:{{env('DEV_EMAIL')}}">{{env('DEV_EMAIL')}}</a></h5>

		</main>
	</div>
@stop