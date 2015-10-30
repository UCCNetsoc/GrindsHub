@extends('layouts.default')


@section('content')
	
	<div class="container row">
		<main class="card-panel white col s12 m8 offset-m2">
			<br /><h2 class="center-align">Make a donation</h2><br />
			@if( count( $errors ) > 0 )
				<div class="row dark-orange white-text">
					@foreach ($errors->all() as $message)
						{{$message}}
					@endforeach
				</div>
			@endif
			{!! Form::open( array('route' => [ 'make/donation'], 'method' => 'post', 'class' => 'row col s12') ) 		!!}
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

				<div class="input-field col s12 m2 offset-m1">
					<input id="amount" type="number" name="amount" class="validate" placeholder="10" value="5">
					<label for="amount">Amount (&euro;)</label>
				</div>
			</div>

			<br />
			<button class="btn waves-effect waves-light" type="submit" name="action">Donate
				<i class="fa fa-credit-card right"></i>
			</button>
		</div>
		{!! Form::close( ) !!}

		</main>
	</div>
@stop