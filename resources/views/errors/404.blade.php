@extends('layouts.default')

@section('title')
404 - Not Found
@stop

@section('extra-css')
<style>
main{
	padding: 0 !important;
}

h3{
	position: absolute;
  margin-left: -520px;
  margin-top: 575px;
}

html{
	line-height: 0;
}

.card .card-image .card-title{
	  padding: 30px;
}
</style>
@stop

@section('content')


	<div class="row container">
        <div class="col s12 m8 offset-m2">
          <div class="card">
            <div class="card-image">
              <img src="{{ URL::to('/') }}/images/charlie2.jpg" />
              <span class="card-title">Shh... Our developer is sleeping (404)</span>
            </div>
        </div>
      </div>
@stop