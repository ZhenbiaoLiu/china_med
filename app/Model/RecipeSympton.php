<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class RecipeSympton extends Model
{
    protected $table = 'tb_recipe_sympton';

    protected $primaryKey = 'i_recsymp_id';

    public $timestamps = false;

    protected $fillable = [
    	'i_recipe_id',
    	'i_symp_id',
        'v_recsymp_observation',
        'i_recsymp_special',
    	'i_recsymp_status'
    ];

    protected $guarded = [
    	
    ];
}
