<?php

namespace App\Models;

use App\CoreUsers;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'commesse';
    protected $primaryKey = 'id_commessa';
    protected $fillable = [
        'nome',
        'note',
        'attiva',
    ];

    public function orderWorks()
    {
        return $this->hasMany(OrdersWork::class, 'id_commessa', 'id_commessa');
    }

    public function equipments()
    {
        return $this->hasManyThrough(
            EquipmentOrderIntervention::class,
            OrdersWork::class,
            'id_commessa',
            'id_lavoro',
            'id_commessa',
            'id_lavoro'
        );
    }

    public function getWorkingHoursAttribute()
    {
        $hours = $this->orderWorks()->sum('ore_lavorate');
        if ($hours == 0) {
            return '0';
        }
        $hour = floor($hours);
        $min = round(60*($hours - $hour)) ?: '00';
        return $hour . ':' . $min;
    }

    public function getStartDateAttribute()
    {
        if (isset($this->orderWorks()->first()->data)) {
            $startDate = new Carbon($this->orderWorks()->orderBy('data')->first()->data);
            return $startDate->format('d/m/Y');
        }
        return '';
    }

    public function getEndDateAttribute()
    {
        if (isset($this->orderWorks()->first()->data)) {
            $endDate = new Carbon($this->orderWorks()->orderByDesc('data')->first()->data);
            return $endDate->format('d/m/Y');
        }
        return '';
    }

    public function getTechniciansAttribute()
    {
        if ($this->orderWorks()->first()) {
            $techniciansIds = $this->orderWorks()->groupBy('tecnico')->pluck('tecnico');
            foreach ($techniciansIds as $id) {
                $user = CoreUsers::where('id_user', $id)->first();
                $technicians[$id] = $user->name . ' ' . $user->family_name;
            }
            asort($technicians);
            return $technicians;
        }
        return '';
    }
}
