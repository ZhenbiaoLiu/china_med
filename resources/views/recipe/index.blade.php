@extends('layouts.admin')
@section('content')
	<div class="row" >
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
			<h3>Listado de Recetas <a href="receta/create"><button class="btn btn-primary">Nuevo</button></a></h3>
			@include('recipe.search')
		</div>
	</div>

	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="table-responsible">
				<table class="table table-stripped table-bordered table-condensed table-hover">
					<thead>
						<th>Nombre</th>
						<th>Ingredientes</th>
						<th>Estado</th>
						<th>Opciones</th>
					</thead>
					@foreach ($recipes as $r)
						<tr>
							<td>{{ $r->v_recipe_name }}</td>
							<td>
								@if (strlen($r->v_recipe_ingredient) >= 40)
									{{ mb_substr($r->v_recipe_ingredient, 0, 40).'...' }}
								@else
									{{ $r->v_recipe_ingredient }}
								@endif
							</td>
							<td>
								@if ($r->i_recipe_status == 1)
									<?php 
										$stat = 'Apagar'; 
										$btn_type = 'danger'; 
									?>
									<small class="label pull-center bg-green">ON</small>
								@else
									<?php 
										$stat = 'Activar'; 
										$btn_type = 'success'; 
									?>
									<small class="label pull-center bg-red">OFF</small>
								@endif
							</td>
							<td>
								<a href="{{URL::action('RecipeController@edit', $r->i_recipe_id)}}" ><button class="btn btn-info"><i class="fa fa-pencil-square-o"></i> Editar</button></a>
								<a href="{{URL::action('RecipeController@manageRecipeSymptons', $r->i_recipe_id)}}" ><button class="btn btn-warning"><i class="fa fa-stethoscope"></i> Sintomas</button></a>
								<a href="#" data-target="#modal-delete-{{$r->i_recipe_id}}" data-toggle="modal"><button class="btn btn-{{$btn_type}}"><i class="fa fa-power-off"></i> {{$stat}}</button></a>
							</td>
						</tr>
						@include('recipe.modal')
					@endforeach
				</table>
			</div>
			{{$recipes->render()}}
		</div>
	</div>
@endsection