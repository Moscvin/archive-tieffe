<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clienti extends Model
{
    protected $table = 'clienti';
    protected $primaryKey = 'id';
    protected $fillable = [
        'ragione_sociale',
        'partita_iva',
        'codice_fiscale',
        'cliente_visibile',
        'note',
        'partita_iva_errata',
        'committente',
        'alldata'
    ];

    public function getClientNameAttribute() {
        return $this->azienda == 1 ? $this->ragione_sociale : $this->cognome . ' ' . $this->nome;
    }

    public function location() {
        return $this->hasMany('App\Models\Location', 'id_cliente');
    }

    public function locations() {
        return $this->hasMany('App\Models\Location', 'id_cliente');
    }
}
