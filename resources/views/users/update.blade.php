@extends('layouts.default')

@section('title')
Update Your Profile
@stop

@section('content')
	

	<main class="container row">
		<div class="card-panel white col s12">
			<br />
			<div class="center-align">
				@foreach ($errors->all() as $message)
			        <b>{{ $message }}</b>
			    @endforeach
			</div>
			<br />
			{{-- 'first_name', 'last_name', 'main_role', 'rate', 'subjects', 'county', 'picture', 'cover_picture' --}}
			{!! Form::open( array('route' => 'user/store_update', 'method' => 'post', 'class' => 'row col s12', 'enctype' => 'multipart/form-data') ) !!}
			

			@if( URL::previous() == URL::to('/') . '/user/store' )
				<h3 class="center-text"> Welcome to GrindsHub! Start out by adding a profile picture below!</h3>
				<br />
			@endif

			<div class="row">
				<div class="col s12">
					<ul class="tabs">
						<li class="tab col s3"><a class="active" href="#picture">Pictures</a></li>
						<li class="tab col s3"><a href="#your-info">Your Info</a></li>
						<li class="tab col s3"><a onclick="window.location = '{{ URL::route('payments') }}'" href="#">Payments</a></li>
					</ul>
				</div>
				<div id="picture" class="col s12">
					<br />
					<div class="row">
						<div class="col m4 s12">
							<h5 class="center-align">Upload New Profile Picture</h5>
							<figure>
								{!! HTML::image( ( Auth::user( )->picture != '') ? Auth::user( )->picture : '/images/default/profile_picture', null, [ 'width' => '200px'] ) !!}
								<div class="file-field input-field">
									<div class="btn">
										<span>Upload New Profile Picture</span>
										{!! Form::file('picture', ['onChange'=>'$("[type=\'submit\']").prop( "disabled", true );this.form.submit();']) !!}
									</div>
								</div>
							</figure>
						</div>
						<div class="col m7 offset-m1 s12">
							<h5 class="center-align">Upload New Cover Photo</h5>
							<figure>
								{!! HTML::image( ( Auth::user( )->cover_picture != '') ? Auth::user( )->cover_picture : '/images/default/cover_photo', null, [ 'width' => '100%'] ) !!}

								<div class="file-field input-field ">
									<div class="btn ">
										{!! Form::file('cover_picture', ['onChange'=>'$("[type=\'submit\']").prop( "disabled", true );this.form.submit();']) !!}

										<span>Upload New Cover Photo</span>
									</div>
							    </div>
							</figure>
							<br />
							
							
						</div>
					</div> 

					<div class="row">
						<br />
						<br />
						<p class="center-text col s12 m10 offset-m1"><em>Maximum upload size is 2MB. Ideal dimensions for the profile picture are 600px X 600px and the ideal dimensions for your cover picture are 1920px X 1280px.</em></p>
					</div>
				</div>
				<div id="your-info" class="col s12">

					<br />
					<div class="row">
						<div class="input-field col m4 s12">
							{!! Form::label('first_name', 'First Name') !!}
							{!! Form::text('first_name', Auth::user()->first_name) !!}
						</div>
						<div class="input-field col m4 s12">
							{!! Form::label('last_name', 'Last Name') !!}
							{!! Form::text('last_name', Auth::user()->last_name) !!}
						</div>
						<div class="input-field col m4 s12">
							{!! Form::label('rate', 'Rate Per Hour') !!}
							{!! Form::text('rate', Auth::user()->rate) !!}
						</div>
					</div>

					<div class="row">
						<div class="input-field col m6 s12">
							{!! Form::select('main_role', ['student' => 'student', 'teacher' =>'teacher'], Auth::user( )->main_role) !!}
							{!! Form::label('main_role','Main Role') !!}
						</div>
						<div class="input-field col m6 s12 valign">
							{!! Form::select('county', ['Antrim' =>'Antrim','Armagh' =>'Armagh','Carlow' =>'Carlow','Cavan' =>'Cavan','Clare' =>'Clare','Cork' =>'Cork','Derry' =>'Derry','Donegal' =>'Donegal','Down' =>'Down','Dublin' =>'Dublin','Fermanagh' =>'Fermanagh','Galway' =>'Galway','Kerry' =>'Kerry','Kildare' =>'Kildare','Kilkenny' =>'Kilkenny','Laois' =>'Laois','Leitrim' =>'Leitrim','Limerick' =>'Limerick','Longford' =>'Longford','Louth' =>'Louth','Mayo' =>'Mayo','Meath' =>'Meath','Monaghan' =>'Monaghan','Offaly' =>'Offaly','Roscommon' =>'Roscommon','Sligo' =>'Sligo','Tipperary' =>'Tipperary','Tyrone' =>'Tyrone','Waterford' =>'Waterford','Westmeath' =>'Westmeath','Wexford' =>'Wexford','Wicklow' =>'Wicklow'], Auth::user( )->county) !!}
							{!! Form::label('county','County') !!}
						</div>
					</div>

					<div class="row valign-wrapper">
						
						<div class="input-field col m6 s12">
							{!! Form::textarea( 'subjects', Auth::user( )->subjects, [ 'class' => 'materialize-textarea', 'cols'=>'', 'rows'=>'', 'length' => 150] ) !!}
							{!! Form::label( 'subjects', 'Your Subjects (comma-separated)' ) !!}
						</div>
						<div class="input-field col m6 s12">
							{!! Form::textarea( 'bio', Auth::user( )->bio, [ 'class' => 'materialize-textarea', 'cols'=>'', 'rows'=>'', 'length' => 250] ) !!}
							{!! Form::label( 'bio', 'About Yourself (Bio)' ) !!}
						</div>
					</div>

					<br />

					<button class="btn waves-effect waves-light col m4 offset-m4 btn-large" type="submit" name="action">Update
					</button>
				</div>

			</div>


			{{-- <div class="row">
				<button class="btn waves-effect waves-light right" type="submit" name="action">Update
					<i class="mdi-action-done left"></i>
				</button>
			</div> --}}

			

			{!! Form::close() !!}
		</div>
	</main>

@stop