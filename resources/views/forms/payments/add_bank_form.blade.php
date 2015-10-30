@foreach ($errors->all() as $message)
	{{$message}}
@endforeach
{!! Form::open( array('route' => 'store-bank/payments', 'method' => 'post', 'class' => 'row col s12') ) 		!!}
<br />
<div class="row">
	<div class="input-field col s12">
		<input id="IBAN" type="text" name="IBAN" class="validate">
		<label for="IBAN">IBAN</label>
	</div>

	<div class="input-field col s12">
		<input id="BIC" type="text" name="BIC" class="validate">
		<label for="BIC">BIC</label>
		
	</div>
</div>

{!! Form::label('country','County') !!}
{!! Form::select('country', $countries, 'IE', ['class' => 'browser-default']) !!}

{!! Form::label('currency','Currency') !!}
{!! Form::select('currency', $currencies, 'EUR', ['class' => 'browser-default']) !!}

<br />
<button class="btn waves-effect waves-light" type="submit" name="action">Submit
	<i class="mdi-content-add right"></i>
</button>

{!! Form::close( ) !!}