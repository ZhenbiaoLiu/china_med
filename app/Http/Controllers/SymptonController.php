<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\SymptonFormRequest;
use App\Model\Sympton;
use DB;

class SymptonController extends Controller
{
    const STATUS_OFF = 0;
    const STATUS_ON = 1;

    public function __construct(){

    }

    public function index(Request $request){
    	if ($request){
    		$query = trim($request->get('searchText'));
    		$symptons = DB::table('tb_sympton')->where('v_symp_name', 'LIKE', '%'.$query.'%')
    						->orderBy('v_symp_name', 'asc')
    						->paginate(10)
                            ->appends(request()->query());
    		return view('sympton.index', ['symptons'=>$symptons, 'searchText'=>$query]);
    	}
    }

    public function create(){
    	return view('sympton.create');
    }

    public function store(SymptonFormRequest $request){
        $registed = Sympton::where('v_symp_name', $request->get('nombre'))->first();
        if (!$registed) {
            $sympton = new Sympton();
            $sympton->v_symp_name = $request->get('nombre');
            $sympton->i_symp_status = self::STATUS_ON;
            $sympton->save();
        }
        return Redirect::to('sintoma');
    }

    public function show($id){
    	return view('sympton.show',['sympton'=>Sympton::findOrFail($id)]);
    }

    public function edit($id){
    	return view('sympton.edit',['sympton'=>Sympton::findOrFail($id)]);
    }

    public function update(SymptonFormRequest $request, $id){
    	$sympton = Sympton::findOrFail($id);
    	$sympton->v_symp_name = $request->get('nombre');
    	$sympton->update();
    	return Redirect::to('sintoma');
    }

    public function destroy($id){
    	$sympton = Sympton::findOrFail($id);
        if ($sympton->i_symp_status == self::STATUS_ON) {
            $sympton->i_symp_status = self::STATUS_OFF;
        }
        else {
            $sympton->i_symp_status = self::STATUS_ON;
        }
        $sympton->update();
        return Redirect::to('sintoma');
    }
}
