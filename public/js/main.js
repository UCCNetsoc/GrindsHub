$( document ).ready(function($){
	$(".button-collapse").sideNav();
	$('.modal-trigger').leanModal();
	$('select').material_select( );
	$('.tooltipped').tooltip();
	$('.parallax').parallax();
	$('ul.tabs').tabs();


    $('.materialboxed').materialbox();
	Materialize.showStaggeredList('#staggered-list'); 
	$(".dropdown-button").dropdown({ hover: true, constrain_width: false, belowOrigin: true });       
});


function showStars( ){
	var wholeStars, halfStars, result, totalStars = 0;

	result = $('.review-form .rating [type=range]').val( ) / 0.5;
	wholeStars = Math.floor( result / 2 );
	halfStars = result % 2;
	
	$('.review-form .stars').html('');
	
	for (i = 0; i < wholeStars; i++) { 
	    $('.review-form .stars').append( '<i class="fa fa-star"></i>' );
	    totalStars++;
	}

	for (var i = 0; i < halfStars; i++) {
		$('.review-form .stars').append( '<i class="fa fa-star-half-o"></i>' );
		totalStars++;
	};

	for (var i = 0; i < (5 - totalStars); i++) {
		$('.review-form .stars').append( '<i class="fa fa-star-o"></i>' );
	};
	
}