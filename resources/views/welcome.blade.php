<!DOCTYPE HTML>
<html>
	<head>
		<title>GrindsHub - Learning Isn't A Spectator Sport</title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="description" content="" />
		<meta name="keywords" content="" />
		<!--[if lte IE 8]><script src="/css/ie/html5shiv.js"></script><![endif]-->
		<script src="{{ URL::to('/') }}/js/jquery.min.js"></script>
		<script src="{{ URL::to('/') }}/js/jquery.scrollex.min.js"></script>
		<script src="{{ URL::to('/') }}/js/jquery.scrolly.min.js"></script>
		<script src="{{ URL::to('/') }}/js/skel.min.js"></script>
		<script src="{{ URL::to('/') }}/js/init.js"></script>


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


		<noscript>
			<link rel="stylesheet" href="{{ URL::to('/') }}/css/skel.css" />
			<link rel="stylesheet" href="{{ URL::to('/') }}/css/style.css" />
			<link rel="stylesheet" href="{{ URL::to('/') }}/css/style-xlarge.css" />
		</noscript>
		<!--[if lte IE 8]><link rel="stylesheet" href="{{ URL::to('/') }}/css/ie/v8.css" /><![endif]-->
		<!--[if lte IE 9]><link rel="stylesheet" href="{{ URL::to('/') }}/css/ie/v9.css" /><![endif]-->

		<style>
			.sr-only{
				  position: absolute;
				  width: 1px;
				  height: 1px;
				  margin: -1px;
				  padding: 0;
				  overflow: hidden;
				  clip: rect(0, 0, 0, 0);
				  border: 0;
			}
		</style>

		<script>
		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		  ga('create', 'UA-53709669-1', 'auto');
		  ga('send', 'pageview');

		</script>
	</head>
	<body>

		<!-- Header -->
			<section id="header">
				<header class="major">
					<h1 class="sr-only">GrindsHub</h1>
					<img src="/images/logo_white.png"/>
					<p>A way to organise and advertise grinds</p>
				</header>
				<div class="container">
					<ul class="actions">
						<li><a href="{{ URL::route('login') }}" class="button special scrolly">Login</a></li>
						<li><a href="{{ URL::to('/auth/register') }}" class="button special scrolly">Register</a></li>
					</ul>
				</div>
			</section>

		<!-- One -->
			<section id="one" class="main special">
				<div class="container">
					<span class="image fit primary"><img src="/images/pic01.jpg" alt="" /></span>
					<div class="content">
						<header class="major">
							<h2>Finding Grinds Is Hard</h2>
						</header>
						<p>Unless you know a guy who knows a girl who knows an oracle, it can be hard to find someone who's doing grinds for your course. We make it easy, just start off with the searchbox <a href="{{ URL::to('/home') }}">here</a> and find tutors teaching what you want to know in your area. We're here to make it as easy as possible for you to learn what you need to learn. </p>
					</div>
					<a href="#two" class="goto-next scrolly">Next</a>
				</div>
			</section>

		<!-- Two -->
			<section id="two" class="main special">
				<div class="container">
					<span class="image fit primary"><img src="/images/pic02.jpg" alt="" /></span>
					<div class="content">
						<header class="major">
							<h2>Organising Grinds Is Hard</h2>
						</header>
						<p>So you think you've found the perfect tutor but now you don't know what to do? Well, we can help!</p>
						<ul class="icons-grid">
							<li>
								<span class="icon major fa-envelope-o"></span>
								<h3>Message Them</h3>
								<p>We give you messaging to sort out where to meet and when</p>
							</li>
							<li>
								<span class="icon major fa-pencil-square-o"></span>
								<h3>Check Out Reviews</h3>
								<p>Every student has the opportunity to put in a review of their grind(s)</p>
							</li>
								<li>
								<span class="icon major fa-money"></span>
								<h3>Pay Through Us</h3>
								<p>No more remembering to take out money and hoping for change.</p>
							</li>
							<li>
								<span class="icon major fa-star-half-o"></span>
								<h3>What's Their Rating?</h3>
								<p>We give you a tutor's average rating to make it an easy decision</p>
							</li>
						</ul>
					</div>
					<a href="#three" class="goto-next scrolly">Next</a>
				</div>
			</section>

		<!-- Three -->
			<section id="three" class="main special">
				<div class="container">
					<span class="image fit primary"><img src="/images/pic03.jpg" alt="" /></span>
					<div class="content">
						<header class="major">
							<h2>One more thing</h2>
						</header>
						<p>If you're a tutor, we can handle all your payments so you never have that awkward feeling of asking for money.</p>
						<p>We use stripe for all our payment stuff and do not at any point store any of your banking details on our server</p>
					</div>
					<a href="#footer" class="goto-next scrolly">Next</a>
				</div>
			</section>


		<!-- Footer -->
			<section id="footer">
				<div class="container">
					<header class="major">
						<h2>It's dangerous to go it alone.</h2>
					</header>
					<a class="special button" href="{{ URL::to('/auth/register') }}">Come With Us!</a>
				</div>
				<footer>
					
					<ul class="copyright">
						<li>&copy; Grindshub {{ date('Y') }}</li><li><a href="mailto:{{env('DEV_EMAIL')}}">{{env('DEV_EMAIL')}}</a></li>
					</ul>
				</footer>
			</section>

	</body>
</html>