@extends('layouts.admin')
@section('content')
	<div class="row">
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
			<h3>Editar Sintoma: {{$sympton->v_symp_name}} </h3>
			@if (count($errors)>0)
				<div class="alert alert-danger">
					<ul>
						@foreach ($errors->all() as $error)
							<li> {{$error}} </li>
						@endforeach
					</ul>
				</div>
			@endif

			{{Form::model($sympton, ['method'=>'PATCH', 'route'=>['sintoma.update', $sympton->i_symp_id]])}}
				{{Form::token()}}
				<div class="form-group">
					<label for="nombre">Nombre</label>
					<input type="text" name="nombre" class="form-control" value="{{$sympton->v_symp_name}}" placeholder="Nombre...">
				</div>
				<div class="form-group">
					<button class="btn btn-primary" type="summit"><i class="fa fa-floppy-o"></i> Guardar</button>
					<button class="btn btn-danger" type="reset"><i class="fa fa-times"></i> Limpiar</button>
				</div>
			{{Form::Close()}}

		</div>
	</div>
@stop