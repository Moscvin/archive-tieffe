<?php

namespace App\Models;

use App\Models\Operation\Operation;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $table = 'sedi';
    protected $primaryKey = 'id_sedi';
    protected $fillable = [
        'id_cliente',
        'tipologia',
        'indirizzo_via',
        'indirizzo_civico',
        'indirizzo_cap',
        'indirizzo_comune',
        'indirizzo_provincia',
        'telefono1',
        'telefono2',
        'email',
        'note',
        'attivo',
        'alldata'
    ];

    public function client() {
        return $this->belongsTo('App\Models\Clienti', 'id_cliente');
    }

    public function machineries() {
        return $this->hasMany('App\Models\Machinery', 'id_sedi');
    }
    public function macchinari(){
        return $this->belongsTo('App\Models\Machinery', 'id_sedi');
    }
    public function operations() {
        return $this->hasMany(Operation::class, 'id_sede');
    }

    public function getAddressAttribute()
    {
        $address = '';
        if($this->indirizzo_via) {
            $address .= ($this->indirizzo_via . ' ' . $this->indirizzo_civico . ' ');
        }
        if($this->indirizzo_cap) {
            $address .= '(' . $this->indirizzo_cap . ') ';
        }
        if($this->indirizzo_comune) {
            $address .= $this->indirizzo_comune . ' ' . $this->indirizzo_provincia;
        }
        return $address;
    }

    public function getPhonesAttribute()
    {
        $phones = $this->telefono1;
        if($this->telefono2) {
            $phones .= (' ' . $this->telefono2 );
        }
        return $phones;
    }


    public function delete()
    {
        $this->operations->each(function($item) {
            $item->delete();
        });

        $this->machineries->each(function($item) {
            $item->delete();
        });
        return parent::delete();
    }
}
