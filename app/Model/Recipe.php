<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $table = 'tb_recipe';

    protected $primaryKey = 'i_recipe_id';

    public $timestamps = false;

    protected $fillable = [
    	'v_recipe_name',
    	'v_recipe_ingredient',
    	'i_symp_status'
    ];

    protected $guarded = [
    	
    ];
}
