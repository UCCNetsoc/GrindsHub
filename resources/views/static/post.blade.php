@extends('layouts.default')


@section('content')
	
	<div class="row container blog-content">
		<section class="white card-panel">
			<img src="{{$post->image}}" />
			<h1>{{ $post->title }}</h1>
			<p>{!! $post->text !!}</p>
			
			<p><small>{{ ucwords( $post->tags ) }}</small></p>
		</section>
	</div>
@stop