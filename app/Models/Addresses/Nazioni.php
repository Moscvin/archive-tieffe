<?php

namespace App\Models\Addresses;

use Illuminate\Database\Eloquent\Model;

class Nazioni extends Model
{
    protected $table = 'standard_nazioni';
    protected $primaryKey ='id_nazione';
    protected $fillable = [
        'nazione','sigla_nazione',
    ];
}
