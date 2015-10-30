@extends('layouts.payments')


@section('content')

	<div class="col s12 m10 offset-m1">
		
		@if( Auth::user( )->recipient_id == null )
			<br />
			<h5>To accept payments, there are a couple steps to getting setup.</h5>
			<br /><br />
			<h3>What is Stripe?</h4>
			<p> Stripe is a developer-friendly payment-processor. Essentially, it's a website-friendly version of paypal. It makes both our lives easier. After you follow the steps below, you'll never have to worry about it ever again, it's just that simple.</p>
			<br />
			<h4>I do not have a Stripe account</h4>
			<ol>
				<li>We need to sign you up for Stripe (it's like paypal but friendlier). To do that, go to <a href="{{ URL::route('add-bank/payments') }}">Add A Bank</a> and we'll sign you up automagically. You'll then receive an email to claim your account. Just click the link and now you're all set to go.</li>
				<li>You're then going to have to click <a href="https://connect.stripe.com/oauth/authorize?response_type=code&client_id={{ env('STRIPE_CLIENT_ID') }}&scope=read_write">this link</a> to make sure that we can give you money.</li>
			</ol>
			<p>You will then manage all your details on <a href="https://dashboard.stripe.com/account/transfers">Stripe</a>. We do not store any of your card or banking information on this  site.</p> 

			<br />
			<h4> I have a Stripe account already</h4>
			<p>Make sure you're logged into Stripe and then click on the button below</p>
			<a href="https://connect.stripe.com/oauth/authorize?response_type=code&client_id={{ env('STRIPE_CLIENT_ID') }}&scope=read_write" class="waves-effect waves-light btn"><i class="fa fa-cc-stripe left"> Connect To Stripe</i></a>
			<br /><br />
		@endif
		
		<br />
		<h3>How Do Payments work?</h3>
		<ol>
			<li>Get your student's username (it's displayed in their messages)</li>
			<li>Send a <a href="{{ URL::route('request-invoice/payments') }}">Payment Request</a> using their username, the subject you taught them and the amount to be charged</li>
			<li>When someone pays you, GrindsHub gets <b>&euro;3 from the total amount</b> to cover the Stripe fee and pay for the service. <b>So you only pay when you get paid.</b></li>
			<li>You should receive your money into your Stripe account within a day and into your bank account within 7-days. You can change that 7-day setting <a href="https://dashboard.stripe.com/account/transfers">over at Stripe</a>.
		</ol>

		<br /><br />
	</div>
@stop