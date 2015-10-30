@include('layouts.header')

<div class="container">
	<div class="row">
		<br />

		<main class="col m9 card-panel white payment-content">
			@yield('content')

		</main>

		<aside id='payments' class="col m3 card-panel dark-orange white-text">
			<ul>
				<li class="center-align heading"><a href="{{ URL::route( 'payments' ) }}" class="white-text waves-effect waves-orange btn-flat"><i class="fa fa-cc-stripe"></i> Payments</a><div class="divider"></div></li>
				@if( Auth::user()->recipient_id == null )
					<li><a href="{{ URL::route('add-bank/payments') }}" target="_BLANK" class="white-text waves-effect waves-orange btn-flat"><i class="fa fa-credit-card"></i> Add A Bank Account </a></li>
					@if( $count = count( App\Invoice::getOutstandingPayments( Auth::user( )->id ) ) > 0 ) 
						<li><a href="{{ URL::route('outstanding/payments') }}" class="white-text waves-effect waves-orange btn-flat light-blue darken-3"><i class="fa fa-money"></i>  Outstanding <b>({{ $count }})</b> </a></li>

					@else
						<li><a href="{{ URL::route('outstanding/payments') }}" class="white-text waves-effect waves-orange btn-flat"><i class="fa fa-money"></i>  Outstanding </a></li>
					@endif
				@else
					<li><a href="https://dashboard.stripe.com/account/transfers" class="white-text waves-effect waves-orange btn-flat"><i class="fa fa-credit-card"></i> Manage Bank Account</a></li>
					<li><a href="{{ URL::route( 'request-invoice/payments' ) }}" class="white-text waves-effect waves-orange btn-flat"><i class="fa fa-envelope-o"></i>  Request Payment </a></li>
					@if( $count = count( App\Invoice::getOutstandingPayments( Auth::user( )->id ) ) > 0 ) 
						<li><a href="{{ URL::route('outstanding/payments') }}" class="white-text waves-effect waves-orange btn-flat light-blue darken-3"><i class="fa fa-money"></i>  Outstanding <b>({{ $count }})</b> </a></li>

					@else
						<li><a href="{{ URL::route('outstanding/payments') }}" class="white-text waves-effect waves-orange btn-flat"><i class="fa fa-money"></i>  Outstanding </a></li>
					@endif
					<li><a href="{{ URL::route('invoices/payments') }}" class="white-text waves-effect waves-orange btn-flat"><i class="fa fa-book"></i> All Payments </a></li>
				@endif
			</ul>
		</aside>
	</div>
</div>

@include('layouts.footer')