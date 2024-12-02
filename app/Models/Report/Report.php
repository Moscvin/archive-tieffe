<?php

namespace App\Models\Report;

use App\CoreUsers;
use App\Models\EquipmentOrderIntervention;
use App\Models\Intervention;
use App\Models\Operation\Operation;
use App\Models\Operation\OperationMachinery;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Report extends Model
{
    protected $table = 'rapporti';
    protected $primaryKey = 'id_rapporto';
    protected $fillable = [
        'id_rapporto',
        'id_intervento',

        'luogo_intervento',
        'promemoria',
        'mail_send',
        'firma',
        'firmatario',
        'garanzia',
        'stato',
        'incasso_stato',
        'note',
        'fatturato',
        'dafatturare',
        'data_invio',
        'data_intervento',
        'letto',
        'progressivo',
        'incasso_pos',
        'incasso_in_contanti',
        'incasso_con_assegno',
        'piano_intervento',
        'carrello_cingolato',
        'altra_norma_text',
        'raccomandazioni',
        'prescrizioni',
        'UNI_7129',
        'UNI_10683',
        'altra_norma_value',
        "ricerca_perdite",
        "cercafughe",
        "messa_in_pressione",
        "note_riparazione",
        "linea_vita",
        "aggiuntivo"
    ];

    public function operation() {
        return $this->belongsTo(Operation::class, 'id_intervento');
    }

    public function photos() {
        return $this->hasMany(ReportPhoto::class, 'id_rapporto');
    }

    public function equipment() {
        return $this->hasMany(ReportEquipment::class, 'id_rapporto');
    }

    public function intervention() {
        return $this->belongsTo(Intervention::class, 'id_intervento', 'id_intervento');
    }

    public function equipments()
    {
        return $this->hasMany(EquipmentOrderIntervention::class, 'id_intervento', 'id_intervento');
    }

    public function machineries() {
        return $this->hasMany(OperationMachinery::class, 'id_intervento', 'id_intervento');
    }

//TODO move up to a helper?
    public function getFormattedDateAttribute()
    {
        if (isset($this->data_intervento)) {
            $date = new Carbon($this->data_intervento);
            return $date->format('d/m/Y');
        }
        return '';
    }

    public function getStatusTextAttribute()
    {
        $status = '';
        if($this->stato == 2) {
            $status = 'Completato';
        }
        if($this->stato == 3) {
            $status = 'Non completato';
        }
        if($this->stato == 4) {
            $status = 'Annullato';
        }
        return $status;
    }

    public function getStatusColorAttribute()
    {
        $class = '';
        if($this->stato == 2) {
            $class = '';
        }
        if($this->stato == 3) {
            $class = 'not_completed';
        }
        if($this->stato == 4) {
            $class = 'canceled';
        }
        return $class;
    }

    public function getOperationDateAttribute()
    {
        if (isset($this->data_intervento)) {
            $date = new Carbon($this->data_intervento);
            return $date->format('d/m/Y');
        }
        return '';
    }


    public function getTechnicianNamesAttribute()
    {
        if($this->operation->tecnico ?? false) {
            $technicianIds = explode(';', trim($this->operation->tecnico ?? '', ';'));
            if(!empty($technicianIds)) {
                $names = [];
                foreach ($technicianIds as $id) {
                    $technician = CoreUsers::where('id_user', $id)->first();
                    $names[] = $technician->name . ' ' . $technician->family_name;
                }
                return implode('; ', $names);
            }
        }
        return '';
    }

    public function getTechnicianNamesListAttribute()
    {
        if($this->operation->tecnico ?? false) {
            $technicianIds = explode(';', trim($this->operation->tecnico, ';'));
            if(!empty($technicianIds)) {
                $names = [];
                foreach ($technicianIds as $id) {
                    $technician = CoreUsers::where('id_user', $id)->first();
                    $names[] = $technician->name . ' ' . $technician->family_name;
                }
                return $names;
            }
        }
        return '';
    }

    public function getWorkingHoursAttribute()
    {
        return $this->sumHours('ore_lavoro');
    }

    public function getTravelHoursAttribute()
    {
        return $this->sumHours('ore_viaggio');
    }

    public function sumHours($fieldName)
    {
        $hours = 0;
        $minutes = 0;
        for ($i = 1; $i <= 3; $i++) {
            $name = $fieldName . $i;
            if ($this->{$name}) {
                $timeArray = explode(':', $this->$name);
                $hours += $timeArray[0];
                $minutes += $timeArray[1];
            }
        }
        if ($hours > 0 || $minutes > 0) {
            $hours +=  intdiv($minutes, 60);
            $minutes = $minutes % 60;
            return sprintf('%02d:%02d', $hours, $minutes);
        }
        return '';
    }

    public function getTravelKmsAttribute()
    {
        return $this->km_viaggio1 + $this->km_viaggio2 + $this->km_viaggio3;
    }

    public function delete()
    {
        $this->operation->update([
            'stato' => 1
        ]);
        $this->photos->each(function($item) {
            $item->delete();
        });
        $this->equipments()->each(function($item) {
            $item->delete();
        });
        Storage::deleteDirectory('reports/photos/' . $this->id_intervento);
        Storage::deleteDirectory('reports/signatures/' . $this->id_intervento);
        return parent::delete();
    }

    public function getReportNumberAttribute()
    {
        return $this->progressivo;
    }
}
