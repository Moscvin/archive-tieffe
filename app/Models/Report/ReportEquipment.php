<?php

namespace App\Models\Report;

use Illuminate\Database\Eloquent\Model;
use App\Models\Equipment;

class ReportEquipment extends Model
{
    protected $table = 'rapporti_materiali';
    protected $promaryKey = 'id_rapporti_materiali';

    protected $fillable = [
        'id_rapporti_materiali',
        'id_rapporto',
        'id_materiali',
        'quantita',
    ];

    public function report() {
        return $this->belongsTo(Report::class, 'id_rapporto');
    }

    public function equipment() {
        return $this->belongsTo(Equipment::class, 'id_materiali');
    }
}
