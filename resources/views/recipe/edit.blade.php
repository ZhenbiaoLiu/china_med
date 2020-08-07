@extends('layouts.admin')
@section('content')
	<div class="row">
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
			<h3>Editar Receta: {{$recipe->v_recipe_name}} </h3>
			@if (count($errors)>0)
				<div class="alert alert-danger">
					<ul>
						@foreach ($errors->all() as $error)
							<li> {{$error}} </li>
						@endforeach
					</ul>
				</div>
			@endif

			{{Form::model($recipe, ['method'=>'PATCH', 'route'=>['receta.update', $recipe->i_recipe_id]])}}
				{{Form::token()}}
				<div class="form-group">
					<label for="nombre">Nombre</label>
					<input type="text" name="nombre" class="form-control" value="{{$recipe->v_recipe_name}}" placeholder="Nombre...">
				</div>
				<div class="form-group">
					<label for="ingredientes">Ingredientes</label>
					<textarea name="ingredientes" class="form-control" rows="5" placeholder="Receta...">{{$recipe->v_recipe_ingredient}}</textarea>
				</div>
				<div class="form-group">
					<button class="btn btn-primary" type="summit"><i class="fa fa-floppy-o"></i> Guardar</button>
					<button class="btn btn-danger" type="reset"><i class="fa fa-times"></i> Limpiar</button>
				</div>
			{{Form::Close()}}

		</div>
	</div>
@stop