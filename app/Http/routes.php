<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('layouts.admin');
});
/* CRUD de entidades*/
Route::resource('/sintoma','SymptonController');
Route::resource('/receta','RecipeController');
/* Detalle de receta */
Route::get('/receta/{id}/sintoma',['uses'=>'RecipeController@manageRecipeSymptons']);
Route::post('/receta/{id}/sintoma',['uses'=>'RecipeController@saveRecipeSymptons']);
/* CRUD de detalle de receta */
Route::post('/receta/sintoma/delete',['uses'=>'RecipeController@deleteRecipeSympton']);
Route::get('/receta/sintoma/edit/{id}',['uses'=>'RecipeController@editRecipeSymptonDetail']);
Route::post('/receta/sintoma/edit/{id}',['uses'=>'RecipeController@updateRecipeSymptonDetail']);
/* Buscador */
Route::get('/buscador',['uses'=>'RecipeController@browserIndex']);
Route::get('/buscador/consulta',['uses'=>'RecipeController@browseRecipe']);