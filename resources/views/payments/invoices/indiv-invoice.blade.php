@extends('layouts.payments')

@section('content')
<div class="col s12 m10 offset-m1">
	<br />
	<h4>Payment Request</h4>
	<h5>
		From:
		<a class="dark-orange-text" href="{{ URL::route('user/profile') }}">
			{{ $invoice->sender->first_name . ' ' . $invoice->sender->last_name }}
		</a>
		( Date: {{ date( 'dS M Y', strtotime( $invoice->created_at ) ) }} )
	</h5>

	<p>Hey {{ $invoice->recipient->first_name }},</p>
	<p>
		You're an awesome student, I hope the grind went well for you. Be sure to leave a review on 

		<a class="dark-orange-text" href="{{ URL::route('user/profile') }}">
			{{ $invoice->sender->first_name  }}'s profile
		</a>

		 and let your friends know about them. Other than that, have a good day and if you have any questions, send me an email at <a href="mailto:{{env('DEV_EMAIL')}}">{{env('DEV_EMAIL')}}</a>.
	</p>

	<div class="right">
		<strong><h3>Total: &euro;{{ $invoice->amount }}</h3></strong>
	</div>
</div>

<div class="clear"></div>

@if( $invoice->paid == 'no' )
	<div class="center-align col s12 m10 offset-m1">
		<h3 class="left
			@if( count( $errors ) > 0 )
				white-text
			@endif
		"> Payment: </h3>
		
		@if( count( $errors ) > 0 )
			<div class="row dark-orange white-text">
				@foreach ($errors->all() as $message)
					{{$message}}
				@endforeach
			</div>
		@endif
		{!! Form::open( array('route' => [ 'pay-invoice/payments', 	$invoice->id], 'method' => 'post', 'class' => 'row col s12') ) 		!!}
		<br />
		<div class="row">
			<div class="input-field col s12 m9">
				<input id="card_number" type="text" name="card_number" class="validate" placeholder="123456789">
				<label for="card_number">Card Number</label>
			</div>

			<div class="input-field col s12 m3">
				<input id="CVC" type="text" name="CVC" class="validate" placeholder="123">
				<label for="CVC">CVC / Security Number</label>
			</div>
		</div>

		<div class="row">
			<div class="input-field col s12 m2">
				<input id="expiry_month" type="number" name="expiry_month" class="validate" placeholder="1">
				<label for="expiry_month">Expiry Month</label>
			</div>

			<div class="input-field col s12 m2">
				<input id="expiry_year" type="number" name="expiry_year" class="validate" placeholder="2016">
				<label for="expiry_year">Expiry Year</label>
			</div>
		</div>

		<br />
		<button class="btn waves-effect waves-light" type="submit" name="action">pay
			<i class="fa fa-credit-card right"></i>
		</button>
	</div>
	{!! Form::close( ) !!}
@else
	<div class=" col s12 dark-orange center-align">

		<h2 class="white-text">PAID</h2>
	</div>
	<br /><br /><br />
@endif

@stop