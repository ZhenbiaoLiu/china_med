<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\RecipeFormRequest;
use App\Model\Recipe;
use App\Model\Sympton;
use App\Model\RecipeSympton;
use DB;

class RecipeController extends Controller
{
    const STATUS_OFF = 0;
    const STATUS_ON = 1;

    public function __construct(){

    }

    public function index(Request $request){
    	if ($request){
    		$query = trim($request->get('searchText'));
    		$recipes = DB::table('tb_recipe')->where('v_recipe_name', 'LIKE', '%'.$query.'%')
    						->orderBy('v_recipe_name', 'asc')
    						->paginate(10)
                            ->appends(request()->query());
    		return view('recipe.index', ['recipes'=>$recipes, 'searchText'=>$query]);
    	}
    }

    public function create(){
    	return view('recipe.create');
    }

    public function store(RecipeFormRequest $request){
        $registed = Recipe::where('v_recipe_name', $request->get('nombre'))->first();
        if (!$registed) {
            $recipe = new Recipe();
            $recipe->v_recipe_name = $request->get('nombre');
            $recipe->v_recipe_ingredient = $request->get('ingredientes');
            $recipe->i_recipe_status = self::STATUS_ON;
            $recipe->save();
            if ($recipe->i_recipe_id){
                //$symptons = DB::table('tb_sympton')->orderBy('v_symp_name', 'asc')->get();
                return redirect()->action('RecipeController@manageRecipeSymptons', ['id'=>$recipe->i_recipe_id]);
            }else{
                return Redirect::to('receta/create');
            }
        }
        return Redirect::to('receta');
    }

    public function show($id){
    	return view('recipe.show',['recipe'=>Recipe::findOrFail($id)]);
    }

    public function edit($id){
    	return view('recipe.edit',['recipe'=>Recipe::findOrFail($id)]);
    }

    public function update(RecipeFormRequest $request, $id){
    	$recipe = Recipe::findOrFail($id);
    	$recipe->v_recipe_name = $request->get('nombre');
    	$recipe->v_recipe_ingredient = $request->get('ingredientes');
    	$recipe->update();
    	return Redirect::to('receta');
    }

    public function destroy($id){
    	$recipe = Recipe::findOrFail($id);
        if ($recipe->i_recipe_status == self::STATUS_ON) {
            $recipe->i_recipe_status = self::STATUS_OFF;
        }
        else {
            $recipe->i_recipe_status = self::STATUS_ON;
        }
        $recipe->update();
        return Redirect::to('receta');
    }

    public function manageRecipeSymptons($id){
        $recipe = Recipe::findOrFail($id);
        $symptons = DB::table('tb_sympton as s')
                    ->leftJoin('tb_recipe_sympton as rs', function ($join) use ($id) {
                        $join->on('rs.i_symp_id', '=', 's.i_symp_id')
                        ->where('rs.i_recipe_id', '=', $id);
                    })
                    ->select('s.i_symp_id', 's.v_symp_name')
                    ->where('s.i_symp_status', '=', self::STATUS_ON)
                    ->where(function ($query) {
                        $query->whereNull('rs.i_recsymp_id')
                            ->orWhere('rs.i_recsymp_status', '=', self::STATUS_OFF);
                    })
                    ->orderBy('s.v_symp_name', 'asc')
                    ->get();

        $recipe_symps = DB::table('tb_recipe_sympton as rs')
                        ->join('tb_sympton as s', 'rs.i_symp_id', '=', 's.i_symp_id')
                        ->select('rs.i_recsymp_id', 'rs.i_recsymp_special', 's.i_symp_id', 's.v_symp_name')
                        ->where('rs.i_recipe_id', '=', $recipe->i_recipe_id)
                        ->where('rs.i_recsymp_status', '=', self::STATUS_ON)
                        ->where('s.i_symp_status', '=', self::STATUS_ON)
                        ->orderBy('s.v_symp_name', 'asc')
                        ->get();
        return view('recipe.add-sympton', ['recipe'=>$recipe, 'symptons'=>$symptons, 'rec_symp'=>$recipe_symps]);
    }

    public function saveRecipeSymptons(Request $request, $id){
        if ($request->input('sintomas')){
            $stored = DB::table('tb_recipe_sympton')
                        ->where('i_recipe_id', '=', $id)
                        ->lists('i_symp_id');

            foreach ($request->input('sintomas') as $s){
                if (in_array($s, $stored)) {
                    $recipeSymp = RecipeSympton::where('i_recipe_id', '=', $id)
                                            ->where('i_symp_id', '=', $s)
                                            ->firstOrFail();
                    if ($recipeSymp) {
                        $recipeSymp->i_recsymp_status = self::STATUS_ON;
                        $recipeSymp->update();
                    }
                } else {
                    $recipeSymp = new RecipeSympton();
                    $recipeSymp->i_recipe_id = $id;
                    $recipeSymp->i_symp_id = $s;
                    $recipeSymp->i_recsymp_status = self::STATUS_ON;
                    $recipeSymp->save();
                }
            }
        } else {
            return Redirect::to('receta');
        }
        return redirect()->action('RecipeController@manageRecipeSymptons', ['id'=>$id]);
    }

    public function deleteRecipeSympton(Request $request){
        if ($request) {
            $recipeSymp = RecipeSympton::findOrFail($request->input('id'));
            if ($recipeSymp){
                $recipeSymp->i_recsymp_status = self::STATUS_OFF;
                $recipeSymp->update();
                $status = 'ok';
                $msg = 'Sintoma eliminado';
            } else{
                $status = 'error';
                $msg = 'Sintoma no encontrado';
            }
        } else{
            $status = 'error';
            $msg = 'Faltan parametros';
        }
        return response()->json(array('status'=>$status, 'data'=>['msg'=>$msg, 'id'=>$request->input('id')]));
    }

    public function editRecipeSymptonDetail($id){
        $rs = RecipeSympton::findOrFail($id);
        $recipe = Recipe::findOrFail($rs->i_recipe_id);
        $symp = Sympton::findOrFail($rs->i_symp_id);
        return view('recipe-sympton.edit',['recipeSymp'=>$rs, 'recipe'=>$recipe, 'symp'=>$symp]);
    }

    public function updateRecipeSymptonDetail(Request $request, $id){
        $recipeDetail = RecipeSympton::findOrFail($id);
        if ($recipeDetail) {
            $recipeDetail->v_recsymp_observation = $request->get('observe');
            if ($request->get('chkEspecial')) {
                $recipeDetail->i_recsymp_special = 1;
            } else {
                $recipeDetail->i_recsymp_special = 0;
            }
            $recipeDetail->update();
            return redirect()->action('RecipeController@manageRecipeSymptons', ['id'=>$recipeDetail->i_recipe_id]);
        } else {
            return Redirect::to('receta');
        }
    }

    public function browserIndex(){
        $symptons = DB::table('tb_sympton as s')
                    ->select('i_symp_id', 'v_symp_name')
                    ->where('i_symp_status', '=', self::STATUS_ON)
                    ->orderBy('v_symp_name', 'asc')
                    ->get();
        return view('recipe.browser', ['symptons'=>$symptons]);
    }

    public function browseRecipe(Request $request){
        $data = [];
        if ($request) {
            $query_parameters = $request->get('data');
            $searchBy = $request->get('searchBy');
            $take = $request->get('limit');
            $page = $request->get('page');
            $skip = $take * ($page - 1);

            if ($searchBy == "1") {
                $totalResults = count(DB::table('tb_recipe_sympton as rs')
                                ->join('tb_recipe as r', 'rs.i_recipe_id', '=', 'r.i_recipe_id')
                                ->select('r.i_recipe_id')
                                ->whereIn('rs.i_symp_id', $query_parameters)
                                ->where('r.i_recipe_status', '=', self::STATUS_ON)
                                ->where('rs.i_recsymp_status', '=', self::STATUS_ON)
                                ->groupBy('r.i_recipe_id')
                                ->get());

                $recipes = DB::table('tb_recipe_sympton as rs')
                                ->join('tb_recipe as r', 'rs.i_recipe_id', '=', 'r.i_recipe_id')
                                ->select('r.i_recipe_id', 'r.v_recipe_name', 'r.v_recipe_ingredient', DB::raw('count(*) as conteo'))
                                ->whereIn('rs.i_symp_id', $query_parameters)
                                ->where('r.i_recipe_status', '=', self::STATUS_ON)
                                ->where('rs.i_recsymp_status', '=', self::STATUS_ON)
                                ->groupBy('r.i_recipe_id')
                                ->orderBy('conteo', 'desc')
                                ->skip($skip)
                                ->take($take)
                                ->get();
            } else {
                $query = trim($query_parameters[0]);
                if (!empty($query)) {
                    $totalResults = $recipes = DB::table('tb_recipe')->where('v_recipe_name', 'LIKE', '%'.$query.'%')
                                    ->count();

                    $recipes = DB::table('tb_recipe')->where('v_recipe_name', 'LIKE', '%'.$query.'%')
                                    ->orderBy('v_recipe_name', 'asc')
                                    ->skip($skip)
                                    ->take($take)
                                    ->get();
                } else {
                    $recipes = [];
                }
            }

            $recipes_id = array_column($recipes, 'i_recipe_id');

            $recipes_detail = DB::table('tb_recipe as r')
                                ->join('tb_recipe_sympton as rs', 'rs.i_recipe_id', '=', 'r.i_recipe_id')
                                ->join('tb_sympton as s', 'rs.i_symp_id', '=', 's.i_symp_id')
                                ->select('r.i_recipe_id', 's.i_symp_id', 's.v_symp_name', 'rs.i_recsymp_id', 'rs.v_recsymp_observation', 'rs.i_recsymp_special')
                                ->whereIn('r.i_recipe_id', $recipes_id)
                                ->where('r.i_recipe_status', '=', self::STATUS_ON)
                                ->where('rs.i_recsymp_status', '=', self::STATUS_ON)
                                ->orderBy('r.i_recipe_id', 'asc')
                                ->get();

            if ($recipes && $recipes_detail) {
                $data[] = $recipes;
                $data[] = $recipes_detail;
                $data[] = $totalResults;
                $status = 'ok';
                $msg = 'Elementos encontrados';
            } else {
                $status = 'error';
                $msg = 'No se encontro resultados';
            }
        } else {
            $status = 'error';
            $msg = 'Faltan parametros de busqueda';
        }
        return response()->json(array('status'=>$status, 'msg'=>$msg, 'data'=>$data));
    }
}