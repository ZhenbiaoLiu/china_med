@extends('layouts.admin')
@section('content')
	<div class="row" >
		<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
			<h3>Listado de Sintomas <a href="sintoma/create"><button class="btn btn-primary">Nuevo</button></a></h3>
			@include('sympton.search')
		</div>
	</div>

	<div class="row">
		<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
			<div class="table-responsible">
				<table class="table table-stripped table-bordered table-condensed table-hover">
					<thead>
						<th>Nombre</th>
						<th>Estado</th>
						<th>Opciones</th>
					</thead>
					@foreach ($symptons as $s)
						<tr>
							<td>{{ $s->v_symp_name }}</td>
							<td>
								@if ($s->i_symp_status == 1)
									<?php $stat = 'Apagar'; $btn_type = 'danger'; ?>
									<small class="label pull-center bg-green">ON</small>
								@else
									<?php $stat = 'Activar'; $btn_type = 'success'; ?>
									<small class="label pull-center bg-red">OFF</small>
								@endif
							</td>
							<td>
								<a href="{{URL::action('SymptonController@edit', $s->i_symp_id)}}" ><button class="btn btn-info"><i class="fa fa-pencil-square-o"></i> Editar</button></a>
								<a href="#" data-target="#modal-delete-{{$s->i_symp_id}}" data-toggle="modal"><button class="btn btn-{{$btn_type}}"><i class="fa fa-power-off"></i> {{$stat}}</button></a>
							</td>
						</tr>
						@include('sympton.modal')
					@endforeach
				</table>
			</div>
			{{$symptons->render()}}
		</div>
	</div>
@endsection