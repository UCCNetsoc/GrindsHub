<!DOCTYPE html>
<html lang="en">
<head>
	<title>@yield('title', 'GrindsHub')</title>
	<meta charset="utf-8">
	<meta name="description" content="@yield('description')" />

    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<link rel="shortcut icon" href="{{ URL::to('/') }}/images/favicon.png">

	
	<link rel="stylesheet" type="text/css" href="{{ URL::to('/') }}/css/font-awesome.min.css">
	<link rel="stylesheet" href="{{ URL::to('/') }}/css/normalize.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.96.1/css/materialize.min.css">
	<link rel="stylesheet" type="text/css" href="{{ URL::to('/') }}/css/main.css">
	@yield('extra-css')
	
	<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.96.1/js/materialize.min.js"></script>
	<script type="text/javascript" src="{{ URL::to('/') }}/js/main.js"></script>
	@yield('extra-js')

	<!-- HTML5 IE Enabling Script -->
	<!--[if lt IE 9]><script src="{{ URL::to('/') }}/js/html5shiv.min.js"></script>
	<![endif]-->

	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	  ga('create', 'UA-53709669-1', 'auto');
	  ga('send', 'pageview');

	</script>

	@if ($__env->yieldContent('extra-head'))
	    @yield('extra-head')
	@else
		<meta property="og:type" content="website" />
		<meta property="og:title" content="GrindsHub.com - Learning Isn't A Spectator Sport" />
		<meta property="og:url" content="{{ URL::to('/') }}" />
		<meta property="og:description" content="GrindsHub lets students find, talk to and pay tutors in one easy place. Sign up as a teacher to let us handle all the hard stuff and let you focus on advertising your skills." />
		<meta property="og:site_name" content="GrindsHub.com" />
		<meta property="og:image" content="{{ URL::to('/') }}/images/og-image.png" />
		<meta property="og:locale" content="en_GB" />


		<meta name="twitter:card" content="summary_large_image">
		<meta name="twitter:site" content="@thejokersthief">
		<meta name="twitter:creator" content="@thejokersthief">
		<meta name="twitter:title" content="GrindsHub.com - Learning Isn't A Spectator Sport">
		<meta name="twitter:description" content="GrindsHub lets students find, talk to and pay tutors in one easy place. Sign up as a teacher to let us handle all the hard stuff and let you focus on advertising your skills.">
		<meta name="twitter:image" content="{{ URL::to('/') }}/images/og-image.png">
	@endif
	
	 <script type="application/ld+json">
        {
          "@context": "http://schema.org",
          "@type": "Organization",
          "url": "https://grindshub.com",
          "logo": "{{ URL::to('/') }}/images/og-image.png",
          "sameAs" : [ 
            "https://www.facebook.com/GrindsHub",
           ]
        }
    </script>
</head>
<body>
    
    <header>
    	<nav>
			<div class="nav-wrapper container">
			  <a href="{{ URL::to('/home') }}" class="brand-logo">
			  	<figure>
					<img src="/images/logo_white_solo.png" alt="GrindsHub" class="logo">
					<figcaption class="sr-only">
						<h1> GrindsHub </h1>
					</figcaption>
				</figure>
			  </a>
			  <a href="#" data-activates="mobile-demo" class="button-collapse"><i class="mdi-navigation-menu"></i></a>
			  <ul class="right hide-on-med-and-down">
			  	@if( Auth::check( ) )
			  		<li><a href="{{ URL::to( 'home' ) }}">Home</a></li>
					<li><a class="dropdown-button" data-activates="profile-dropdown" href="{{ route( 'user/profile', Auth::user( )->username ) }}">My Profile</a>
						<ul id="profile-dropdown" class="dropdown-content">
						  <li><a href="{{ route( 'user/update' ) }}">Update Profile</a></li>
						</ul>
					</li>
			    	<li><a href="{{ route( 'all/message' ) }}">Messages 
						@if( App\Message::getUnreadCount( Auth::user( )->id ) > 0 )
							({{ App\Message::getUnreadCount( Auth::user( )->id ) }})
						@endif
			    	</a></li>
			    	<li>
			    		<a href="{{ route( 'payments' ) }}" class="dropdown-button" data-activates="payments-dropdown">Payments
							@if( $count = count( App\Invoice::getOutstandingPayments( Auth::user( )->id ) ) > 0  )
								({{ $count }})
							@endif
			    		</a>
						<ul id="payments-dropdown" class="dropdown-content">
							<li><a href="https://dashboard.stripe.com/account/transfers"><i class="fa fa-credit-card left"></i> Manage Bank Account</a></li>
							<li><a href="{{ URL::route( 'request-invoice/payments' ) }}"><i class="fa fa-envelope-o left"></i>  Request Payment </a></li>
							@if( $count = count( App\Invoice::getOutstandingPayments( Auth::user( )->id ) ) > 0 ) 
								<li><a href="{{ URL::route('outstanding/payments') }}" class="white-text light-blue darken-3"><i class="fa fa-money left"></i>  Outstanding <b>({{ $count }})</b> </a></li>

							@else
								<li><a href="{{ URL::route('outstanding/payments') }}"><i class="fa fa-money left"></i>  Outstanding </a></li>
							@endif
							<li><a href="{{ URL::route('invoices/payments') }}"><i class="fa fa-book left"></i> All Payments </a></li>
							</uL>	
					   </li>
					   <li><a href="{{ URL::route('donation') }}">Donate</a></li>
					   <li><a href="{{ URL::to('https://thejokersthief.typeform.com/to/t25xGc') }}">Suggestions</a></li>
			  	@else 
			  		<li><a href="{{ URL::to('home') }}">Search</a></li>
					<li class="login"><a class="waves-effect waves-light modal-trigger" href="#login-modal">Login</a></li>
					<li><a href="{{ URL::route('register') }}">Register</a></li>
					<li><a href="{{ URL::route('donation') }}">Donate</a></li>
					<li><a href="{{ URL::to('https://thejokersthief.typeform.com/to/t25xGc') }}">Suggestions</a></li>
			  	@endif
			  </ul>
			  <ul class="side-nav" id="mobile-demo">

			  	@if( Auth::check( ) )
			  		<li><a href="{{ URL::to( 'home' ) }}">Home</a></li>
					<li><a href="{{ route( 'user/profile', Auth::user( )->username ) }}">My Profile</a></li>
					<li><a href="{{ route( 'user/update' ) }}">Update Profile</a></li>
			    	<li><a href="{{ route( 'all/message' ) }}">Messages 
						@if( App\Message::getUnreadCount( Auth::user( )->id ) > 0 )
							({{ App\Message::getUnreadCount( Auth::user( )->id ) }})
						@endif
			    	</a></li>
			    	<li><a href="{{ route( 'payments' ) }}">Payments
						@if( $count = count( App\Invoice::getOutstandingPayments( Auth::user( )->id ) ) > 0  )
							({{ $count }})
						@endif
			    	</a></li>
			    	<li><a href="{{ URL::route('donation') }}">Donate</a></li>
			    	<li><a href="{{ URL::to('https://thejokersthief.typeform.com/to/t25xGc') }}">Suggestions</a></li>
			    
			  	@else 
			  		<li><a href="{{ URL::to('home') }}">Search</a></li>
					<li><a href="{{ URL::route('login') }}">Login</a></li>
			    	<li><a href="{{ URL::route('register') }}">Register</a></li>
			    	<li><a href="{{ URL::route('donation') }}">Donate</a></li>
			    	<li><a href="{{ URL::to('https://thejokersthief.typeform.com/to/t25xGc') }}">Suggestions</a></li>
			  	@endif
			 
			  </ul>
			</div>
		</nav>
	</header>