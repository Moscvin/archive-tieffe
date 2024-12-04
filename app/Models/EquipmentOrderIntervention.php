<?php

namespace App\Models;

use App\CoreUsers;
use Illuminate\Database\Eloquent\Model;

class EquipmentOrderIntervention extends Model
{
    protected $table = 'materiali_comm_interv';
    protected $primaryKey = 'id_materiale';

    protected $fillable = [
        'id_lavoro',
        'id_intervento',
        'codice',
        'descrizione',
        'quantita',
    ];

    public function orderWork() {
        return $this->belongsTo(OrdersWork::class, 'id_lavoro', 'id_lavoro');
    }
    public function intervention() {
        return $this->belongsTo(Intervention::class, 'id_intervento', 'id_intervento');
    }
}
