<?php

namespace App\Repositories;

use App\Models\Clienti;
use Illuminate\Support\Facades\DB;

class ClientRepository
{
    public function getById($id)
    {
        return Clienti::where('id', $id)->with('user')->first();
    }

    public function getAppUsers()
    {
        return Clienti::where('cliente_borrauto', 0)->get();
    }

    public function getAppUsersByName($value)
    {
        return Clienti::where('cliente_borrauto', 0)->where(function ($query) use ($value) {
            $query->where('ragione_sociale', 'like', '%' . $value . '%')->orWhereRaw('CONCAT(cognome, " ", nome) LIKE ?', '%' . $value . '%');
        })->get();
    }

    public function getFiltered($fields, $chars = [])
    {
        if (in_array("W", $chars) && !in_array("V", $chars)) {
            $query = Clienti::where('id_user', auth()->id());
        } else {
            $query = Clienti::query();
        }

        if(strlen($fields['value'])) {
            switch((int)($fields['select'] ?? -1)) {
                case 1: {
                    $query->where('ragione_sociale', 'like', '%' . $fields['value'] . '%');
                    break;
                }
                case 2: {
                    $query->where('partita_iva', 'like', '%' . $fields['value'] . '%');
                    break;
                }
                case 3: {
                    $query->where('codice_fiscale', 'like', '%' . $fields['value'] . '%');
                    break;
                }
                case 4: {
                    $query->whereHas('location', function ($q) use ($fields) {
                      $q->where('indirizzo_via', 'like', '%' . $fields['value'] . '%');
                    });
                    break;
                }
                case 5: {
                  $query->whereHas('location', function ($q) use ($fields) {
                    $q->where('indirizzo_comune', 'like', '%' . $fields['value'] . '%');
                  });
                  break;
                }
                case 6: {
                  $query->whereHas('location', function ($query) use ($fields) {
                      $query->where(DB::raw('CONCAT_WS(", ", telefono1, telefono2)'), 'like', '%' . $fields['value'] . '%');
                  });
                }
            }
        }
        foreach($fields['order'] ?? [] as $condition) {
            switch($condition['column']) {
                case 1: {
                    $query->orderBy('ragione_sociale',  $condition['dir']);
                    break;
                }
                case 2: {
                    $query->orderBy('partita_iva', $condition['dir']);
                    break;
                }
                case 3: {
                    $query->orderBy('codice_fiscale', $condition['dir']);
                    break;
                }
            }
        }

        return (object)[
            'items' => (clone $query)->skip($fields['start'])->take($fields['length'])->get(),
            'recordsFiltered' => (clone $query)->count(),
            'recordsTotal' => Clienti::count()
        ];
    }
}
