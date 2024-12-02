<?php

namespace App\Models\Operation;

use Illuminate\Database\Eloquent\Model;
use App\CoreUsers as User;
use App\Models\Location;
use App\Models\Clienti;
use App\Models\Report\Report;
use App\Models\EquipmentOrderIntervention;

class Operation extends Model
{
    protected $table = 'interventi';
    protected $primaryKey = 'id_intervento';
    protected $fillable = [
        'tipologia',
        'ora_dalle',
        'ora_alle',
        'incasso',
        'cestello',
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
        'old_id_intervento',
        'fatturare_a',
    ];

    public function headquarter() {
        return $this->belongsTo(Location::class, 'id_sede');
    }

    public function previousOperation() {
        return $this->belongsTo(Operation::class, 'old_id_intervento');
    }

    public function replanOperation() {
        return $this->hasOne(Operation::class, 'old_id_intervento');
    }

    public function lastReportCanceled() {
        return $this->hasOne(Report::class, 'id_intervento');
    }

    public function location() {
        return $this->hasOne('App\Models\Location', 'id_sedi');
    }

    public function getFileNameAttribute()
    {
        $fileName = str_replace("operation/","",$this->file);
        return $fileName;
    }

    public function getCorpoTextAttribute(){

        return $this->a_corpo == 1 ? 'Si' : 'No';

    }

    public function invoice_client(){

        return $this->belongsTo(Clienti::class, 'fatturare_a', 'id');
    }

    public function report() {
        return $this->hasOne(Report::class, 'id_intervento');
    }

    public function internalWorks() {
        return $this->hasMany('App\Models\InternalWork', 'id_intervento');
    }

    public function reports() {
        return $this->hasMany(Report::class, 'id_intervento');
    }

    public function dailyWork() {
        return $this->hasMany('App\Models\InternalWork', 'id_intervento');
    }

    public function getTechniciansArrayAttribute()
    {
        $ids = preg_split('/;/', $this->tecnico);
        array_pop($ids);
        return $ids;
    }

    public function technicians()
    {
        return User::where('id_group', 9)
            ->whereIn('id_user', $this->techniciansArray)->when(count($this->techniciansArray), function($q) {
                $q->orderByRaw("FIELD(id_user, " . implode(',', $this->techniciansArray) . ")");
            })->get();
    }

    public function machineries()
    {
        return $this->hasMany(OperationMachinery::class, 'id_intervento');
    }

    public function equipments()
    {
        return $this->hasMany(EquipmentOrderIntervention::class, 'id_intervento');
    }

    public function delete()
    {
        if($this->replanOperation) {
            $this->replanOperation->update([
                'old_id_intervento' => null
            ]);
        }

        $this->reports->each(function($report) {
            $report->delete();
        });

        $this->machineries->each(function($machinery) {
            $machinery->delete();
        });

        $this->equipments->each(function($equipment) {
            $equipment->delete();
        });

        return parent::delete();
    }

    public function mainTechnician()
    {
        return User::where('id_user', $this->techniciansArray[0] ?? 0)->first();
    }

    public function oldOperation() {
        return $this->belongsTo(Operation::class, 'old_id_intervento');
    }
}
