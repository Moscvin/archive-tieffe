<?php

namespace App\Models;

use App\Models\Report\Report;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Intervention extends Model
{
    protected $table = 'interventi';
    protected $primaryKey = 'id_intervento';
    protected $fillable = [
        'tipologia',
        'ora_dalle',
        'ora_alle',
        'incasso',
        'data',
        'ora',
        'urgente',
        'a_corpo',
        'tecnico',
        'pronto_intervento',
        'stato',
        'file',
        'note',
        'id_sede',
        'long',
        'lat',
        'old_id_intervento'
    ];

    public function location()
    {
        return $this->belongsTo(Location::class, 'id_sede', 'id_sedi');
    }
    public function report()
    {
        return $this->belongsTo(Report::class, 'id_intervento', 'id_intervento');
    }
    public function materials()
    {
        return $this->belongsTo(EquipmentOrderIntervention::class, 'id_intervento', 'id_intervento');
    }
    public function intervention()
    {
        return $this->belongsTo(Intervention::class, 'id_intervento', 'id_intervento');
    }


    public function getFormattedDateAttribute()
    {
        if (isset($this->data)) {
            $date = new Carbon($this->data);
            return $date->format('d/m/Y');
        }
        return '';
    }
}
