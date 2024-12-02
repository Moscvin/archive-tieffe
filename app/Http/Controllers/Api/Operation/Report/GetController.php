<?php

namespace App\Http\Controllers\Api\Operation\Report;

use App\Models\Clienti;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Report\Report;
use App\Models\Operation\Operation;
use App\Models\Operation\OperationMachinery;
use App\Models\Location;

class GetController extends Controller
{
    public function main(Request $request, $idOperation, $id)
    {
        try {
            $report = Report::find($id);
            $operationMacchinari = OperationMachinery::with('machinery')->where('id_intervento', $idOperation)->first();
            $response = [
                'id_intervento' => $report->id_intervento,
                'luogo_intervento' => $report->luogo_intervento ?? '',
                'promemoria' => $report->promemoria ?? 0,
                'mail_send' => $report->mail_send ?? '',
                'firmatario' => $report->firmatario,
                'garanzia' => $report->garanzia ?? 0,
                'stato' => $report->stato ?? 0,
                'incasso_stato' => $report->incasso_stato ?? null,
                'note' => $report->note ?? '',
                'fatturato' => $report->fatturato,
                'dafatturare' => $report->dafatturare,
                'data_invio' => $report->data_invio,
                'data_intervento' => $report->data_intervento,
                'letto' => $report->letto,
                'piano_intervento' => $report->piano_intervento,
                'UNI_7129' => $report->UNI_7129,
                'UNI_10683' => $report->UNI_10683,
                'altra_norma_text' => $report->altra_norma_text,
                'altra_norma_value' => $report->altra_norma_value,
                'raccomandazioni' => $report->raccomandazioni,
                'prescrizioni' => $report->prescrizioni,
                'carrello_cingolato' => $report->carrello_cingolato,
                'rapporti_materiali' => $report->equipments->map(function($item) {
                    return (object)[
                        'id_materiale' => $item->id_materiale,
                        'codice' => $item->codice,
                        'descrizione' => $item->descrizione,
                        'quantita' => $item->quantita,
                    ];
                }),
                'photos' => $report->photos->map(function($item) {
                    return \URL::to('/') . "/file/$item->filename";
                }) ?? [],
                'incasso_pos' => $report->incasso_pos,
                'incasso_in_contanti' => $report->incasso_in_contanti,
                'incasso_con_assegno' => $report->incasso_con_assegno,
                'ricerca_perdite' => $report->ricerca_perdite,
                'cercafughe' => $report->cercafughe,
                'messa_in_pressione' => $report->messa_in_pressione,
                'note_riparazione' => $report->note_riparazione,
                'linea_vita' => $report->linea_vita,
            ];
            return response()->json($response, 200);
        } catch(\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ], 500);
        }
    }
}
