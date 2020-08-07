@extends('layouts.admin')
@section('content')
	<div class="row">
		<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
			<h3>Agregar Sintomas a <i class="fa fa-arrow-right"></i> <span class="label label-success">{{$recipe->v_recipe_name}}</span></h3>

			<div class="form-group">
				<label>Agregar Sintomas</label>
				<div class="row">
					<div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
						<select name="psintoma" class="form-control selectpicker" id="psintoma" data-live-search="true" data-size="6">
							@foreach($symptons as $s)
								<option value="{{$s->i_symp_id}}">{{$s->v_symp_name}}</option>
							@endforeach
						</select>
					</div>
				</div>
			</div>

			<div class="form-group">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<label>Sintomas registrados:</label>
					</div>
					<div class="panel-body">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							@foreach($rec_symp as $rs)
								<div id="symp{{$rs->i_recsymp_id}}" style="display: inline-block">
									<h4><span class="label {{empty($rs->i_recsymp_special) ? 'label-default' : 'label-info'}}"> <a href="{{URL::action('RecipeController@editRecipeSymptonDetail', $rs->i_recsymp_id)}}">{{$rs->v_symp_name}}</a> <a class="disposable" data-target="#modal-confirm" data-toggle="modal" data-id="{{$rs->i_recsymp_id}}" href="#" style="margin-left:5px">x</a></span></h4>
								</div>
							@endforeach
						</div>
					</div>
				</div>
			</div>

			{{Form::open(['action'=>['RecipeController@saveRecipeSymptons', $recipe->i_recipe_id], 'method'=>'POST'])}}
			{{Form::token()}}
			<div class="form-group">
				<div class="panel panel-info">
					<div class="panel-heading">
						<label>Sintomas a registrar:</label>
					</div>
					<div class="panel-body">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="addSymp">
						</div>	
					</div>
				</div>
			</div>

			<div class="form-group">
				<button class="btn btn-primary" type="summit"><i class="fa fa-floppy-o"></i> Guardar</button>
				<a href="/receta"><button class="btn btn-danger" type="button"><i class="fa fa-times"></i> Cancelar</button></a>
			</div>
			{{Form::Close()}}
		</div>
		@include('recipe.modal-confirmation')
	</div>

@push('scripts')
<script>
	$(document).ready(function () {
		$("#psintoma").val('default');
	});

	$(document).on("click", "a.disposable", function () {
		id = $(this).data('id');
		$("#modal-confirm .modal-body p").html("Confirme para eliminar");
		$("#modal-confirm .modal-footer").show();
	});

	id=0;

	$(document).on("click", "#modal-confirm .btn-send", function () {
		var request = $.ajax({
			url: "/receta/sintoma/delete",
			method: "POST",
			headers: {
            	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        	},
			data: {id : id}
		});
		
		request.done(function (e) {
			if (e.status == "ok") {
				$("#symp"+e.data.id).remove();
			}
			$("#modal-confirm .modal-body p").html(e.data.msg);
			$("#modal-confirm .modal-footer").hide();
			id = 0;
		});
	});

	$("#psintoma").change(function () {
		idSymp = $("#psintoma").val();
		nomSymp = $("#psintoma option:selected").text();
		if (document.getElementById('close'+idSymp)) {
			alert('Ya se encuentra agregado a lista');
		} else {
			var objSymp = '<div class="to-add" style="display: inline-block; margin-right: 10px;"><h4><span class="label label-info"><input type="hidden" name="sintomas[]" value="'+idSymp+'">'+nomSymp+'<a class="standing" id="close'+idSymp+'" href="#" style="margin-left:5px">x</a></span></h4></div>';
			$("#addSymp").append(objSymp);
		}
		$("#psintoma").val('default');
		$("#psintoma").selectpicker("refresh");
	});

	$(document).on("click", "a.standing", function () {
		$(this).parentsUntil("div#addSymp").remove();
	});

</script>
@endpush
@endsection