<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Operation\Operation;

class InternalWork extends Model
{
    protected $table = 'lavori_interni';
    protected $primaryKey = 'id_lavori_interni';
    protected $fillable = [
        'id_intervento',
        'id_tecnico',
        'tipo',
        'id_intervento',
        'tipo_lavoro',
        'data_ora_inizio',
        'data_ora_fine',
        'note',
    ];

    public function operation() {
        return $this->belongsTo(Operation::class, 'id_intervento');
    }

    public function tehnician() {
        return $this->belongsTo('App\CoreUsers', 'id_tecnico');
    }
}
