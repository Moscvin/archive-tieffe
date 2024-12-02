<?php

namespace App\Models\Addresses;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $table = 'standard_province';
    protected $primaryKey = 'id_provincia';
    protected $fillable = ['sigla_provincia'];

}
