<?php

namespace App\Models;

use App\CoreUsers;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class OrdersWork extends Model
{
    protected $table = 'commesse_lavori';
    protected $primaryKey = 'id_lavoro';
    protected $fillable = [
        'id_commessa',
        'tecnico',
        'data',
        'tipo',
        'ore_lavorate',
        'descrizione'
    ];

    public function order() {
        return $this->belongsTo(Order::class, 'id_commessa');
    }

    public function equipments()
    {
        return $this->hasMany(EquipmentOrderIntervention::class, 'id_lavoro', 'id_lavoro');
    }

    public function getTechnicianNameAttribute()
    {
        $technician = CoreUsers::where('id_user', $this->tecnico)->first();
        return $technician->name. ' ' . $technician->family_name;
    }

    public function getFormattedDateAttribute()
    {
        if (isset($this->data)) {
            $date = new Carbon($this->data);
            return $date->format('d/m/Y');
        }
        return '';
    }

    public function getHoursAttribute()
    {
        $hours = $this->ore_lavorate;
        if ($hours == 0) {
            return '0:00';
        }
        $hour = floor($hours);
        $min = round(60*($hours - $hour)) ?: '00';
        return $hour . ':' . $min;
    }
}
