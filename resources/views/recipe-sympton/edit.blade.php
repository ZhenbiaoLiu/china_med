@extends('layouts.admin')
@section('content')
	<div class="row">
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
			<h3>Editar Sintoma <span class="label label-success">{{$symp->v_symp_name}}</span> adjunto a Receta <span class="label label-success">{{$recipe->v_recipe_name}}</span> </h3>

			{{Form::model($recipe, ['method'=>'POST', 'action'=>['RecipeController@updateRecipeSymptonDetail', $recipeSymp->i_recsymp_id]])}}
				{{Form::token()}}
				<div class="form-group">
					<label for="observe">Observacion</label>
					<textarea name="observe" class="form-control" rows="5" placeholder="Breve nota...">{{$recipeSymp->v_recsymp_observation}}</textarea>
				</div>
				<div class="form-group">
					<label><input type="checkbox" name="chkEspecial" value="1" @if (!empty($recipeSymp->i_recsymp_special)) {{"checked"}} @endif>  Sintoma diferenciador</label>
				</div>
				<div class="form-group">
					<button class="btn btn-primary" type="summit"><i class="fa fa-floppy-o"></i> Guardar</button>
					<button class="btn btn-danger" type="reset"><i class="fa fa-times"></i> Limpiar</button>
				</div>
			{{Form::Close()}}

		</div>
	</div>
@stop