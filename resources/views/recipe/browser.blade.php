@extends('layouts.admin')
@section('content')
	<div id="data-content" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="row" >
			<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
				<h3>Buscador</h3>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
				<div class="box box-solid box-primary">
	                <div class="box-header with-border">
	                	<h2 class="box-title">Buscar</h2>
	                	<div class="box-tools pull-right">
	                    	<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
	                    	<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
	                	</div>
	                </div>
	                <!-- /.box-header -->
	                <div class="box-body">
						<div class="row">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<label><h4>Buscar por: </h4></label>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<label><input type="radio" name="optSearchType" value="1" checked>Sintomas</label>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<label><input type="radio" name="optSearchType" value="2">Nombre de Receta</label>
							</div>
						</div>

						<div class="row">
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-4">
	                			<label>Seleccionar Sintomas</label>
	                		</div>
	                		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-2">	                			
	                		</div>
	                		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
	                			<label>Ingresar receta</label>
	                		</div>
	                	</div>

	                	<div class="row">
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-4">
								<div class="form-group">
									<select name="psintoma" class="form-control selectpicker" id="psintoma" data-live-search="true" data-size="6">
										@foreach($symptons as $s)
											<option value="{{$s->i_symp_id}}">{{$s->v_symp_name}}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-2">
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
								<div class="form-group">
									<input type="text" class="form-control" name="searchText" placeholder="Nombre receta..." value="">
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<div class="form-group">
									<div class="panel panel-info">
										<div class="panel-heading">
											<label>Sintomas seleccionados</label>
										</div>
										<div class="panel-body">
											<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="querySympContainer">

											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<div class="form-group">
									<button class="btn btn-primary" id="searchBtn" type="button"><i class="fa fa-search"></i> Buscar</button>
									<button class="btn btn-danger" id="clearBtn" type="button"><i class="fa fa-eraser"></i> Limpiar</button>
								</div>
							</div>
						</div>						
	                </div>

	                <div class="overlay hidden">
						<i class="fa fa-refresh fa-spin"></i>
					</div>
	            </div>
			</div>
		</div>
	</div>

	<ul class="pagination">
	</ul>
@push('scripts')
<script>
	$(document).ready(function () {
		$("#psintoma").val('default');
		$("input[type='text'][name='searchText']").prop('disabled', true);
		$(".dropdown-toggle").prop('disabled', false);
	});

	var recipe;
	var r_detail;
	var result_total;
	var current_page = 1;
	var per_page = 5;
	var num_pages = 0;
	var data_ids = [];
	var searchType = "1";

	/* Bloqueo de criterio de busqueda */
	$('input[name ="optSearchType"]').click(function () {
		searchType = $("input[type='radio'][name='optSearchType']:checked").val();
		if (searchType == "1") {
			$("input[type='text'][name='searchText']").prop('disabled', true);
			$(".dropdown-toggle").prop('disabled', false);
		} else {
			$("input[type='text'][name='searchText']").prop('disabled', false);
			$(".dropdown-toggle").prop('disabled', true);
		}
	});

	/* Captura de valor del picker de sintomas */
	$("#psintoma").change(function () {
		idSymp = $("#psintoma").val();
		nomSymp = $("#psintoma option:selected").text();
		if (document.getElementById('close'+idSymp)) {
			alert('Ya se encuentra agregado a lista');
		} else {
			var objSymp = '<div id="query'+idSymp+'" style="display: inline-block; margin-right: 10px;"><h4><span class="label label-warning">'+nomSymp+'<a class="query" id="close'+idSymp+'" data-id="'+idSymp+'" href="#" style="margin-left:5px">x</a></span></h4></div>';
			$("#querySympContainer").append(objSymp);
		}
		$("#psintoma").val('default');
		$("#psintoma").selectpicker("refresh");
	});

	/* Opcion pin resultado */
	$(document).on("click", ".btn-lock", function () {
		$(this).closest('.box').removeClass('box-success');
		$(this).closest('.box').addClass('box-info');
		$(this).closest('.resultRow').addClass("locked");
	});

	/* Eliminar sintoma de filtro */
	$(document).on("click", "a.query", function () {
		id = $(this).data('id');
		$("#query"+id).remove();
	});

	/* Funcion boton Buscar */
	$(document).on("click", "#searchBtn", function () {
		searchType = $("input[type='radio'][name='optSearchType']:checked").val();
		current_page = 1;
		$(".pagination").empty();
		captureSearchParameters();
		requestResult();		
	});

	/* Control de paginado */
	$(document).on("click", "a.pager", function () {
		goint_to = $(this).data('page');
		if (goint_to > 0 && goint_to <= num_pages && goint_to != current_page) {
			current_page = $(this).data('page');
			$(".pagination").empty();
			requestResult();
		}
	});

	/* Funcion boton Limpiar */
	$(document).on("click", "#clearBtn", function () {
		$("#querySympContainer").empty();
		data_ids = [];
		$(".pagination").empty();
		limpiar("all");
	});

	/* Captura de criterio de busqueda */
	function captureSearchParameters() {
		data_ids = [];
		if (searchType === "1") {
			$("a.query").each(function (){
				data_ids.push($(this).data('id'));
			});
		} else {
			data_ids.push($("input[type='text'][name='searchText']").val());
		}
	}

	/* Mostrar resultados en el buscador */
	function showResults() {
		recipe.forEach( function(item) {
			id = item.i_recipe_id;
			if (document.getElementById("detailContainer"+id) == null) {
				name = item.v_recipe_name;
				ingredient = item.v_recipe_ingredient;
				var objResultView = '<div class="row resultRow"><div class="col-lg-10 col-md-10 col-sm-10 col-xs-12"><div class="box box-solid box-success"><div class="box-header with-border"><h2 class="box-title">'+name+'</h2><div class="box-tools pull-right"><button class="btn btn-box-tool btn-lock" data-widget="pin"><i class="fa fa-lock"></i></button><button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button><button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>	</div> </div> <div class="box-body"><div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">	<div class="form-group"><div class="panel panel-info"><div class="panel-heading"><label>Ingredientes</label></div><div class="panel-body"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">	<h4>'+ingredient+'</h4></div></div>	</div></div></div></div><div class="row"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div class="form-group"><div class="panel panel-info"><div class="panel-heading">	<label>Sintomas relacionados</label></div><div class="panel-body"><div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><div id="highlightedDetailContainer'+id+'" style="display: inline;"></div><div id="detailContainer'+id+'" style="display: inline;"></div></div></div></div></div></div></div></div></div></div></div>';
				$("#data-content").append(objResultView);
			} else {
				$("#highlightedDetailContainer"+id).empty();
				$("#detailContainer"+id).empty();
			}
		});
		r_detail.forEach( function(item) {
			recSympId = item.i_recsymp_id;
			if (document.getElementById("rs"+recSympId) == null) {
				id = item.i_recipe_id;
				sympId = item.i_symp_id;
				sympName = item.v_symp_name;
				obs = '';
				iconSpecial = '';
				if (jQuery.inArray(sympId, data_ids) !== -1) {
					lb_class = "success";
					containerName = "#highlightedDetailContainer"+id;
				} else {
					lb_class = "default";
					containerName = "#detailContainer"+id;
				}
				if (item.v_recsymp_observation !== null && item.v_recsymp_observation !== '') {
					obs = '<span class="badge">'+item.v_recsymp_observation+'</span>';
				}
				if (item.i_recsymp_special === 1) {
					iconSpecial = '<i class="fa fa-check-square"></i>';
				}
				var objDetail = '<div id="rs'+recSympId+'" style="display: inline-block; margin-right: 10px;"><h4><span class="label label-'+lb_class+'">'+sympName+'  '+obs+iconSpecial+'</span></h4></div>';
				$(containerName).append(objDetail);
			}
		});
	}
	/* Paginator generator */
	function paginate() {
		var paginator = '';
		if (current_page == 1) {
			add_class = ' class="disabled" ';
		} else {
			add_class = '';
		}
		paginator += '<li'+add_class+'><a class="pager" data-page="'+(current_page-1)+'" href="#">«</a></li>';

		if (num_pages > 10) {
			if (current_page <= 4) {
				paginator += createPageElement(1, 5);
			} else  {
				paginator += createPageElement(1, 1);
			}
			paginator += '<li class="disabled"><a class="pager" href="#">...</a></li>';
			if (current_page > 4 && current_page <= (num_pages - 4)) {
				paginator += createPageElement((current_page - 2), (current_page + 2));
				paginator += '<li class="disabled"><a class="pager" href="#">...</a></li>';
			}
			if (current_page > (num_pages - 4) && current_page <= (num_pages)) {
				paginator += createPageElement((num_pages - 4), num_pages);
			} else {
				paginator += createPageElement(num_pages, num_pages);
			}
		} else {
			paginator += createPageElement(1, num_pages);
		}

		if (current_page == num_pages) {
			add_class = ' class="disabled" ';
		} else {
			add_class = '';
		}
		paginator += '<li'+add_class+'><a class="pager" data-page="'+(current_page+1)+'" href="#">»</a></li>';
		$(".pagination").append(paginator);
	}
	/* Generate paginator pages by loop */
	function createPageElement(startPage, lastPage) {
		paginator = '';
		for (var i=startPage; i<=lastPage; i++) {
			if (current_page == i) {
				add_class = ' class="active" ';
			} else {
				add_class = '';
			}
			paginator += '<li'+add_class+'><a class="pager" data-page="'+i+'" href="#">'+i+'</a></li>';
		}
		return paginator;
	}

    /** Agregar busqueda personalizada por receta o por sintomas */
	function requestResult() {
		limpiar();
		$(".overlay").removeClass('hidden');

		var request = $.ajax({
			url: "/buscador/consulta",
			method: "GET",
			headers: {
            	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        	},
			data: {data : data_ids, searchBy : searchType, limit : per_page, page : current_page}
		});
		
		request.done(function (e) {
			$(".overlay").addClass('hidden');
			if (e.status == "ok") {
				recipe = e.data[0];
				r_detail = e.data[1];
				showResults();
				result_total = e.data[2];
				num_pages = Math.ceil(result_total/per_page);
				paginate();
			} else {
				alert(e.msg);
			}
		});
	}

	function limpiar(clean_method) {
		$(".resultRow").each(function (){
			if (clean_method != null && clean_method == "all") {
				$(this).remove();
			} else {
				if ($(this).hasClass("locked") == false) {
					$(this).remove();
				}
			}
		});
	}
</script>
@endpush
@endsection