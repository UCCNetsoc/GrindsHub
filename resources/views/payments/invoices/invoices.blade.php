@extends('layouts.payments')

@section('content')
	@if( count( $invoices ) > 0 )
		<div class="collection">
		@foreach( $invoices as $invoice )
			<a href="{{ URL::route( 'indiv-invoice/payments', \Crypt::encrypt( $invoice->id ) ) }}" class="collection-item">&euro;{{ $invoice->amount }} For: {{ $invoice->subject }} To: {{ $invoice->recipient_username }}
				@if( $invoice->paid == 'yes' )
					<span class="badge paid green"></span>
				@else
					<span class="badge unpaid red"></span>
				@endif
			</a>
		@endforeach	
		</div>
	@else
		<div style="height: 400px;" class="valign-wrapper container">
			<strong class="valign center-align">You have no invoices yet. Are you looking to <a href="#!">request a payment</a> from someone?</strong>
		</div>
	@endif

@stop