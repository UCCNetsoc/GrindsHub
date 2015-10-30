@extends('layouts.payments')

@section('content')
	
	@if( Auth::user( )->recipient_id == null )
		@include( 'forms.payments.add_bank_form' )
	@else
		<div style="height: 400px;" class="valign-wrapper container">
			<strong class="valign center-align">You can manage your bank account and all your account details over at <a href="https://dashboard.stripe.com/account/transfers">Stripe</a></strong>
		</div>
	@endif

@stop