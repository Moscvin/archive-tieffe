<?php

namespace App\Models\Operation;

use Illuminate\Database\Eloquent\Model;
use App\Models\Machinery;

class OperationMachinery extends Model
{
    protected $table = 'interventi_macchinari';
    protected $primaryKey = 'id_macint';
    protected $fillable = [
        'id_intervento',
        'id_macchinario',
        'desc_intervento',
        'rapporto_initial',
        'rapporto_state',
    ];

    public function operation()
    {
        return $this->belongsTo(Operation::class, 'id_intervento');
    }

    public function machinery()
    {
        return $this->belongsTo(Machinery::class, 'id_macchinario');
    }
}
