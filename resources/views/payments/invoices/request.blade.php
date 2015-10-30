@extends('layouts.payments')

@section('content')
@foreach ($errors->all() as $message)
	{{$message}}
@endforeach


{!! Form::open( array('route' => 'create-invoice/payments', 'method' => 'post', 'class' => 'row col s12') ) 		!!}
<br />
<div class="row">
	<div class="input-field col s6">
		<input id="username" type="text" name="username" class="validate" autofocus>
		<label for="username">To: (Username)</label>
	</div>

	<div class="input-field col s6">
		<input id="amount" type="number" name="amount" class="validate" >
		<label for="amount">Amount (positive integers only - no cents)</label>
		
	</div>
	<div class="input-field col s12">
		<input id="subject" type="text" name="subject" class="validate" >
		<label for="subject">Subject (What you taught)</label>
	</div>


</div>

<br />
<button class="btn waves-effect waves-light" type="submit" name="action">Submit
	<i class="mdi-content-add right"></i>
</button>

{!! Form::close( ) !!}	
@stop