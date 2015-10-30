@extends( 'layouts.default' )

@section('title')
	{{ trim( $friendInfo->first_name ) }}'s Messages
@stop

@section( 'content' )
	<main class="container row message-thread">
		<br />
		<section id="user-profile" class="messages card-panel white row hide-on-small-only">
			<div class="col m2">
				<img src="{{ $friendInfo->picture }}" />
			</div>
			<section id="info" class="m9 col">
				<h1>{{ $friendInfo->first_name.' '.$friendInfo->last_name }}</h1>
				<h3>({{ $friendInfo->username }})</h3>				
			</section>
		</section>

		<div class="row">
			<section class="m4 s12 col card-panel white">

				{!! Form::open( array( 'route' => array( 'add/message', $friendInfo->username ), 'method' => 'post' ) ) !!}

				<div class="row">
					<div class="input-field col s12">
						<i class="mdi-action-announcement prefix hide-on-small-only"></i>
						{!! Form::textarea( 'message', null, [ 'class' => 'materialize-textarea', 'cols'=>'', 'rows'=>'', 'length' => 1000] ) !!}
						{!! Form::label( 'message', 'Message' ) !!}
					</div>
				</div>
				<button class="btn waves-effect waves-light" type="submit" name="action">
					Send
					<i class="mdi-content-send right"></i>
				</button>
				
				{{-- Use an encrypted user ID --}}
				{!! Form::hidden( 'receiver', $userId ) !!}


				{!! Form::close( ) !!}
				<br />
			</section>

			<section id="messages" class="m7 s12 offset-m1 col">
				@foreach( $messages as $message )
					@if( $message->sender == Auth::user( )->id )
						<div class="card-panel white">
							<div class="row message">
								<div class="m9 s8 col offset-m1 message-bubble sender">
									<div class="flow-text">{{ $message->message }}</div>
									<br /><em><small>{{ date( "H:i jS F", strtotime( $message->created_at ) ) }}</small></em>
								</div>
								<figure class="m2 s4 col">
									<img src="{{ $senderInfo->picture }}" />
									<b>{{ $senderInfo->first_name }}</b>
									<br><em>{{ $senderInfo->main_role }}</em>
								</figure>
							</div>
						</div>
					@else
						<div class="card-panel white">
							<div class="row message">
								<figure class="m2 s4 col">
									<img src="{{ $friendInfo->picture }}" />
									<b>{{ $friendInfo->first_name }}</b>
									<br><em>{{ $friendInfo->main_role }}</em>
								</figure>
								<div class="m9 s8 col message-bubble receiver">
									<div class="flow-text">{{ $message->message }}</div>
									<br /><em><small>{{ date( "H:i jS F", strtotime( $message->created_at ) ) }}</small></em>
								</div>
							</div>
						</div>
					@endif
				@endforeach
			</section>
		</div>
	</main>
@stop