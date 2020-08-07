<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Sympton extends Model
{
    protected $table = 'tb_sympton';

    protected $primaryKey = 'i_symp_id';

    public $timestamps = false;

    protected $fillable = [
    	'v_symp_name',
    	'i_symp_status'
    ];

    protected $guarded = [
    	
    ];
}
