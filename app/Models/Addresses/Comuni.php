<?php

namespace App\Models\Addresses;

use Illuminate\Database\Eloquent\Model;

class Comuni extends Model
{
    protected $table = 'standard_comuni';
    protected $primaryKey = 'id_comune';
    protected $fillable = [
        'comune','id_provincia','cap'
    ];

    public function provice(){

        return $this->belongsTo('App\Models\Addresses\Province','id_provincia','id_provincia');
    }

}
