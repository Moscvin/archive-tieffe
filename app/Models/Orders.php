<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    protected $table = 'commesse';
    protected $primaryKey = 'id_commessa';
    protected $fillable = [
        'nome',
        'note',
        'attiva'
    ];

    public function OrdersWork(){
        return $this->belongsTo('App\Models\OrdersWork', 'id_commessa');
    }

}
