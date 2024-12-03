<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InterventEquipmentOrders extends Model
{
    protected $table = 'materiali_comm_interv';
    protected $primaryKey = 'id_materiale';
    protected $fillable = [
    	'id_lavoro',
        'id_intervento',
        'codice',
        'descrizione',
        'quantita'
    ];

    public function intervention() {
        return $this->belongsTo(Intervention::class, 'id_intervento', 'id_intervento');
    }
    public function setCodiceAttribute($value)
    {
        $this->attributes['codice'] = substr($value, 0, 100);
    }

    public function setDescrizioneAttribute($value)
    {
        $this->attributes['descrizione'] = substr($value, 0, 255);
    }
    
    public function order() {
        return $this->belongsTo(Order::class, 'id_commessa');
    }
}
