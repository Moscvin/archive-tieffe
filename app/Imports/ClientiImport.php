<?php

namespace App\Imports;

use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\Clienti;
use App\Models\Location;
use App\Models\Machinery;
use App\Models\Intervention;
use App\Models\Report\Report;
use App\Models\Operation\OperationMachinery;

class ClientiImport implements ToCollection
{
    /**
     * @param Collection $rows
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

//// *****    Freddo    *****

    public function collection(Collection $rows)
    {
        $clienti = Clienti::with('location')->pluck('ragione_sociale');

        $found = [];
        foreach ($rows as $key => $row) {
            if ($key > 0) {
                foreach ($clienti as $item) {
                    if ($row[0] == $item) {
                        $found[] = $item;
                    }
                }
            }
        }

        foreach ($rows as $key => $row) {

            if ($key > 0) {
                if (!in_array($row[0], $found)) {
                    $clienti = Clienti::create([
                        'ragione_sociale' => $row[0],
                        'cliente_visibile' => 1,
                    ]);

                    $sedi = Location::create([
                        'id_cliente' => $clienti->id,
                        'tipologia' => 'Sede legale',
                        'indirizzo_via' => $row[1],
                        'indirizzo_comune' => $row[2],
                        'telefono1' => empty($row[3]) ? $row[4] : $row[3],
                        'telefono2' => $row[4],
                        'email' => $row[5],
                    ]);

                    $macchinari = Machinery::create([
                        'id_sedi' => $sedi->id_sedi,
                        'descrizione' => empty($row[7]) ? '' : $row[7],
                        'note' => $row[6],
                        'tipologia' => 'Freddo',
                    ]);

                    $this->checkIfDate($row[13], $sedi, $macchinari);
                    $this->checkIfDate($row[14], $sedi, $macchinari);
                    $this->checkIfDate($row[15], $sedi, $macchinari);
                    $this->checkIfDate($row[16], $sedi, $macchinari);
                    $this->checkIfDate($row[17], $sedi, $macchinari);
                }
            }
        }
    }

// *****    Caldo (Stufa a Pellet)    *****
//    public function collection(Collection $rows)
//    {
//
//        $clienti = Clienti::with('location')->pluck('ragione_sociale');
//
//        $found = [];
//        foreach ($rows as $key => $row) {
//            if ($key > 0) {
//                foreach ($clienti as $item) {
//                    if ($row[0] == $item) {
//                        $found[] = $item;
//                    }
//                }
//            }
//        }
//
//        foreach ($rows as $key => $row) {
//            if ($key > 0) {
//                if (!in_array($row[0], $found)) {
//                    $clienti = Clienti::create([
//                        'ragione_sociale' => $row[0],
//                        'cliente_visibile' => 1,
//                    ]);
//
//                    $sedi = Location::create([
//                        'id_cliente' => $clienti->id,
//                        'tipologia' => 'Sede legale',
//                        'indirizzo_via' => $row[1],
//                        'indirizzo_comune' => $row[2],
//                        'telefono1' => empty($row[3]) ? $row[4] : $row[3],
//                        'telefono2' => $row[4],
//                        'email' => $row[5],
//                    ]);
//
//                    $macchinari = Machinery::create([
//                        'id_sedi' => $sedi->id_sedi,
//                        'descrizione' => empty($row[7]) ? '' : $row[7],
//                        'note' => $row[6],
//                        'tipologia' => 'Caldo',
//                        'C_matr_anno' => $row[8],
//                        'tipo_impianto' => 'Stufa a Pellet',
//                    ]);
//
//                    $this->checkIfDate($row[9], $sedi, $macchinari);
//                    $this->checkIfDate($row[10], $sedi, $macchinari);
//                    $this->checkIfDate($row[11], $sedi, $macchinari);
//                    $this->checkIfDate($row[12], $sedi, $macchinari);
//                    $this->checkIfDate($row[13], $sedi, $macchinari);
//                    $this->checkIfDate($row[14], $sedi, $macchinari);
//                    $this->checkIfDate($row[15], $sedi, $macchinari);
//                    $this->checkIfDate($row[16], $sedi, $macchinari);
//                    $this->checkIfDate($row[17], $sedi, $macchinari);
//                    $this->checkIfDate($row[18], $sedi, $macchinari);
//                    $this->checkIfDate($row[19], $sedi, $macchinari);
//                    $this->checkIfDate($row[20], $sedi, $macchinari);
//                    $this->checkIfDate($row[21], $sedi, $macchinari);
//                    $this->checkIfDate($row[22], $sedi, $macchinari);
//                    $this->checkIfDate($row[23], $sedi, $macchinari);
//                }
//            }
//        }
//    }

// *****    Caldo (Camino Legna Aperto)   *****
//
//    public function collection(Collection $rows)
//    {
//        foreach ($rows as $key => $row) {
//
//            if ($key > 0) {
//                $clienti = Clienti::create([
//                    'ragione_sociale' => $row[0],
//                    'cliente_visibile' => 1,
//                ]);
//
//                $sedi = Location::create([
//                    'id_cliente' => $clienti->id,
//                    'tipologia' => 'Sede legale',
//                    'indirizzo_via' => $row[1],
//                    'indirizzo_comune' => $row[2],
//                    'telefono1' => empty($row[3]) ? $row[4] : $row[3],
//                    'telefono2' => $row[4],
//                ]);
//
//                $macchinari = Machinery::create([
//                    'id_sedi' => $sedi->id_sedi,
//                    'descrizione' => empty($row[6]) ? '' : $row[6],
//                    'tipologia' => 'Caldo',
//                    'tipo_impianto' => 'Camino Legna Aperto',
//                ]);
//
//                $this->checkIfDate($row[15], $sedi, $macchinari);
//                $this->checkIfDate($row[16], $sedi, $macchinari);
//                $this->checkIfDate($row[17], $sedi, $macchinari);
//                $this->checkIfDate($row[18], $sedi, $macchinari);
//                $this->checkIfDate($row[19], $sedi, $macchinari);
//            }
//        }
//    }


//    public function collection(Collection $rows)
//    {
//
//        foreach ($rows as $key => $row) {
//
//            if ($key > 0) {
//                 Clienti::create([
//                    'id' => (int)$row[0],
//                    'ragione_sociale' => $row[1],
//                ]);
//            }
//        }
//    }

//    public function collection(Collection $rows)
//    {
//
//        foreach ($rows as $key => $row) {
//            if ($key > 0) {
//                $intervento = Intervention::where('id_sede', $row[1])->first();
//                $macchinario = Machinery::where('id_sedi', $row[1])->first();
//
//                OperationMachinery::create([
//                    'id_intervento' => $intervento->id_intervento,
//                    'id_macchinario' => $macchinario->id_macchinario,
//                ]);
//
//
//            }
//        }
//
//    }


//    public function collection(Collection $rows)
//    {
//
//        foreach ($rows as $key => $row) {
//            if ($key > 0) {
//                Report::create([
//                    'id_intervento' => $row[1],
//                    'stato' => 2,
//                    'letto' => 1,
//                    'progressivo' => 1,
//                    'data_intervento' => Carbon::createFromFormat('d/m/Y', $row[3])->format('Y-m-d'),
//                    'data_invio' => !empty($row[4]) ? Carbon::createFromFormat('d/m/Y', $row[4])->format('Y-m-d') : null,
//                    'note' => $row[5],
//                ]);
//            }
//        }
//
//    }


//    public function collection(Collection $rows)
//    {
//
//        foreach ($rows as $key => $row) {
//            if ($key > 0) {
//
//                Intervention::create([
//                    'tipologia' => $row[6],
//                    'data' =>  Carbon::createFromFormat('d/m/Y', $row[3])->format('Y-m-d'),
//                    'tecnico' => '2;',
//                    'stato' => 2,
//                    'pronto_intervento' => 0,
//                    'id_sede' => $row[1],
//                    'note' => $row[5],
//                    'ora_alle' => '14:00:00',
//                    'ora_dalle' => '09:00:00',
//                ]);
//
//
//            }
//        }
//
//    }


    public function checkIfDate($date, $sedi, $macchinari)
    {
        if (DateTime::createFromFormat('d-m-y', $date) !== false && $date !== null) {
            $newformat = Carbon::createFromFormat('d-m-y', $date)->format('Y-m-d');

            $interventi = Intervention::create([
                'tipologia' => 'Freddo',
                'stato' => 2,
                'id_sede' => $sedi->id_sedi,
                'pronto_intervento' => 0,
                'data' => $newformat,
                'tecnico' => '2;',
                'ora_alle' => '14:00:00',
                'ora_dalle' => '09:00:00',

            ]);

            Report::create([
                'id_intervento' => $interventi->id_intervento,
                'stato' => 2,
                'letto' => 1,
                'progressivo' => 1,
                'data_invio' => $newformat,
                'data_intervento' => $newformat,
            ]);

            OperationMachinery::create([
                'id_intervento' => $interventi->id_intervento,
                'id_macchinario' => $macchinari->id_macchinario,
            ]);
        }

    }
}

