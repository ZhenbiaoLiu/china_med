{!! Form::open(array('url'=>'receta', 'method'=>'get','autocomplete'=>'off','role'=>'search')) !!}
	<div class="form-group">
		<div class="input-group">
			<input type="text" class="form-control" name="searchText" placeholder="Buscar..." value="{{$searchText}}">
			<span class="input-group-btn">
				<button type="summit" class="btn btn-default">Buscar</button>
			</span>
		</div>
	</div>

{{Form::close()}}