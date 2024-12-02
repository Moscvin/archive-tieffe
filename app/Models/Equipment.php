<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    protected $table = 'materiali';
    protected $primaryKey = 'id_materiali';

    protected $fillable = [
        'id_materiali',
        'denominazione_materiali',
        'attivo',
    ];
}
