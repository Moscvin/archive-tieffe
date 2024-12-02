<?php

namespace App\Http\Controllers\Api\Operation\Report;

use App\CoreUsers;
use App\Models\Clienti;
use App\Models\Machinery;
use App\Models\Location;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Operation\Operation;
use App\Models\Operation\OperationMachinery;
use App\Models\Report\Report;
use App\Models\Report\ReportPhoto;
use App\Models\InterventEquipmentOrders;
use Illuminate\Support\Facades\Storage;

class NewController extends Controller
{
    public function main(Request $request, $idOperation)
    {

        Storage::put('newReportRequest.txt', json_encode($request->all()));

        try {


            Operation::where('id_intervento', $idOperation)->update([
                'stato' => $request->stato
            ]);

            foreach ($request->sede ?? [] as $sedi){
                Location::where('id_sedi', $sedi['id_sede'])->update([
                    'indirizzo_via' => isset($sedi['indirizzo_via']) ? $sedi['indirizzo_via'] : null,
                    'indirizzo_civico' => isset($sedi['indirizzo_civico']) ? $sedi['indirizzo_civico'] : null,
                    'indirizzo_cap' => isset($sedi['indirizzo_cap']) ? $sedi['indirizzo_cap'] : null,
                    'indirizzo_comune' => isset($sedi['indirizzo_comune']) ? $sedi['indirizzo_comune'] : null,
                    'indirizzo_provincia' => isset($sedi['indirizzo_provincia']) ? $sedi['indirizzo_provincia'] : null,
                    'telefono1' => isset($sedi['telefono1']) ? $sedi['telefono1'] : null,
                    'telefono2' => isset($sedi['telefono2']) ? $sedi['telefono2'] : null,
                    'email' => $sedi['email'],
                    'alldata' => 1,
                ]);
            }

            foreach ($request->cliente ?? [] as $cliente){
                Clienti::where('id', $cliente['cliente_id'])->update([
                    'ragione_sociale' => $cliente['cliente_denominazione'],
                    'partita_iva' => $cliente['cliente_partita_iva'],
                    'codice_fiscale' => $cliente['cliente_codice_fiscale'],
                    'alldata' => 1,
                ]);
            }

            foreach ($request->macchinario ?? [] as $macchinario) {
                Machinery::where('id_macchinario', $macchinario['id_macchinario'])->update([
                    'descrizione' => $macchinario['descrizione'],
                    'modello' => $macchinario['modello'] ?? null,
                    'matricola' => $macchinario['matricola'] ?? null,
                    'anno' => $macchinario['anno'] ?? null,
                    'note' => $macchinario['note'] ?? null,
                    'tetto' => $macchinario['tetto'] ?? null,
                    '2tecnici' => $macchinario['2tecnici'] ?? null,
                    'SF_split' => $macchinario['SF_split'] ?? null,
                    'SF_canalizzato' => $macchinario['SF_canalizzato'] ?? null,
                    'SF_predisp_presente' => $macchinario['SF_predisp_presente'] ?? null,
                    'SF_imp_el_presente' => $macchinario['SF_imp_el_presente'] ?? null,
                    'SF_mq_locali' => $macchinario['SF_mq_locali'] ?? null,
                    'SF_altezza' => $macchinario['SF_altezza'] ?? null,
                    'SF_civile' => $macchinario['SF_civile'] ?? null,
                    'SF_indust_commer' => $macchinario['SF_indust_commer'] ?? null,
                    'SC_posizione_cana' => $macchinario['SC_posizione_cana'] ?? null,
                    'SC_certif_canna' => $macchinario['SC_certif_canna'] ?? null,
                    'SC_cana_da_intubare' => $macchinario['SC_cana_da_intubare'] ?? null,
                    'SC_metri' => $macchinario['SC_metri'] ?? null,
                    'SC_materiale' => $macchinario['SC_materiale'] ?? null,
                    'SC_ind_com' => $macchinario['SC_ind_com'] ?? null,
                    'SC_tondo_oval' => $macchinario['SC_tondo_oval'] ?? null,
                    'SC_sezione' => $macchinario['SC_sezione'] ?? null,
                    'SC_tetto_legno' => $macchinario['SC_tetto_legno'] ?? null,
                    'SC_distanze' => $macchinario['SC_distanze'] ?? null,
                    'SC_curve' => $macchinario['SC_curve'] ?? null,
                    'SC_passotetto' => $macchinario['SC_passotetto'] ?? null,
                    'tipo_impianto' => $macchinario['tipo_impianto'] ?? null,
                    'F_anno_aquisto' => $macchinario['F_anno_aquisto'] ?? null,
                    'F_tipo_gas' => $macchinario['F_tipo_gas'] ?? null,
                    'C_costruttore' => $macchinario['C_costruttore'] ?? null,
                    'C_matr_anno' => $macchinario['C_matr_anno'] ?? null,
                    'C_nominale' => $macchinario['C_nominale'] ?? null,
                    'C_combustibile' => $macchinario['C_combustibile'] ?? null,
                    'C_tiraggio' => $macchinario['C_tiraggio'] ?? null,
                    'C_uscitafumi' => $macchinario['C_uscitafumi'] ?? null,
                    'C_libretto' => $macchinario['C_libretto'] ?? null,
                    'C_LA_locale' => $macchinario['C_LA_locale'] ?? null,
                    'C_LA_idoneo' => $macchinario['C_LA_idoneo'] ?? null,
                    'C_LA_presa_aria' => $macchinario['C_LA_presa_aria'] ?? null,
                    'C_LA_presa_aria_idonea' => $macchinario['C_LA_presa_aria_idonea'] ?? null,
                    'C_KRA_dimensioni' => $macchinario['C_KRA_dimensioni'] ?? null,
                    'C_KRA_materiale' => $macchinario['C_KRA_materiale'] ?? null,
                    'C_KRA_coibenza' => $macchinario['C_KRA_coibenza'] ?? null,
                    'C_KRA_curve90' => $macchinario['C_KRA_curve90'] ?? null,
                    'C_KRA_lunghezza' => $macchinario['C_KRA_lunghezza'] ?? null,
                    'C_KRA_idoneo' => $macchinario['C_KRA_idoneo'] ?? null,
                    'C_CA_tipo' => $macchinario['C_CA_tipo'] ?? null,
                    'C_CA_materiale' => $macchinario['C_CA_materiale'] ?? null,
                    'C_CA_sezione' => $macchinario['C_CA_sezione'] ?? null,
                    'C_CA_dimensioni' => $macchinario['C_CA_dimensioni'] ?? null,
                    'C_CA_lunghezza' => $macchinario['C_CA_lunghezza'] ?? null,
                    'C_CA_cam_raccolta' => $macchinario['C_CA_cam_raccolta'] ?? null,
                    'C_CA_cam_raccolta_ispez' => $macchinario['C_CA_cam_raccolta_ispez'] ?? null,
                    'C_CA_curve90' => $macchinario['C_CA_curve90'] ?? null,
                    'C_CA_curve45' => $macchinario['C_CA_curve45'] ?? null,
                    'C_CA_curve15' => $macchinario['C_CA_curve15'] ?? null,
                    'C_CA_piombo' => $macchinario['C_CA_piombo'] ?? null,
                    'C_CA_liberaindipendente' => $macchinario['C_CA_liberaindipendente'] ?? null,
                    'C_CA_innesti' => $macchinario['C_CA_innesti'] ?? null,
                    'C_CA_rotture' => $macchinario['C_CA_rotture'] ?? null,
                    'C_CA_occlusioni' => $macchinario['C_CA_occlusioni'] ?? null,
                    'C_CA_corpi_estranei' => $macchinario['C_CA_corpi_estranei'] ?? null,
                    'C_CA_cambiosezione' => $macchinario['C_CA_cambiosezione'] ?? null,
                    'C_CA_restringe' => $macchinario['C_CA_restringe'] ?? null,
                    'C_CA_diventa' => $macchinario['C_CA_diventa'] ?? null,
                    'C_CA_provatiraggio' => $macchinario['C_CA_provatiraggio'] ?? null,
                    'C_CA_tiraggio' => $macchinario['C_CA_tiraggio'] ?? null,
                    'C_CA_tettolegno' => $macchinario['C_CA_tettolegno'] ?? null,
                    'C_CA_distanze_sicurezza' => $macchinario['C_CA_distanze_sicurezza'] ?? null,
                    'C_CA_certificazione' => $macchinario['C_CA_certificazione'] ?? null,
                    'C_KCO_dimensioni' => $macchinario['C_KCO_dimensioni'] ?? null,
                    'C_KCO_forma' => $macchinario['C_KCO_forma'] ?? null,
                    'C_KCO_cappelloterminale' => $macchinario['C_KCO_cappelloterminale'] ?? null,
                    'C_KCO_zonareflusso' => $macchinario['C_KCO_zonareflusso'] ?? null,
                    'C_KCO_graditetto' => $macchinario['C_KCO_graditetto'] ?? null,
                    'C_KCO_accessotetto' => $macchinario['C_KCO_accessotetto'] ?? null,
                    'C_KCO_comignolo' => $macchinario['C_KCO_comignolo'] ?? null,
                    'C_KCO_tipocomignolo' => $macchinario['C_KCO_tipocomignolo'] ?? null,
                    'C_KCO_idoncomignolo' => $macchinario['C_KCO_idoncomignolo'] ?? null,
                    'C_KCO_cestello' => $macchinario['C_KCO_cestello'] ?? null,
                    'C_KCO_ponteggio' => $macchinario['C_KCO_ponteggio'] ?? null,
                    'alldata' => 1,
                ]);
            }

            $intervento = Operation::where('id_intervento', $idOperation)->first();

            $report = Report::create([
                'id_intervento' => $idOperation,
                'piano_intervento' => $request->piano_intervento ?? '',
                'luogo_intervento' => $request->luogo_intervento ?? '',
                'promemoria' => $request->promemoria ?? 0,
                'mail_send' => str_replace(['Email: ', 'Email:'], ['', ''], $request->mail_send ?? ''),
                'firmatario' => $request->firmatario,
                'garanzia' => (in_array($intervento->tipologia,['Sopralluogo Caldo', 'Sopralluogo Freddo',]))? 0 : (boolean) $request->garanzia,
                'stato' => $request->stato ?? 0,
                'incasso_stato' => $request->incasso_stato ?? null,
                'note' => $request->note ?? '',
                'fatturato' => 0,
                'dafatturare' => intval($request->dafatturare),
                'data_invio' => date('Y-m-d'),
                'data_intervento' => $request->data_intervento ?? null,
                'firma' => null,
                'progressivo' => $this->getProgressive($request),
                'incasso_pos' => $request->incasso_pos,
                'incasso_in_contanti' => $request->incasso_in_contanti,
                'incasso_con_assegno' => $request->incasso_con_assegno,
                'carrello_cingolato' => $request->carrello_cingolato ?? 0,
                'altra_norma_text' => $request->altra_norma_text ?? null,
                'raccomandazioni' => $request->raccomandazioni ?? null,
                'prescrizioni' => $request->prescrizioni ?? null,
                'UNI_7129' => $request->UNI_7129 ?? 0,
                'UNI_10683' => $request->UNI_10683 ?? 0,
                'altra_norma_value' => $request->altra_norma_value ?? 0,
                'ricerca_perdite' => $request->ricerca_perdite,
                'cercafughe' => $request->cercafughe,
                'messa_in_pressione' => $request->messa_in_pressione,
                'note_riparazione' => $request->note_riparazione,
                'linea_vita' => $request->linea_vita ?? 0,
                'aggiuntivo' => (int) $request->aggiuntivo
            ]);

            foreach($request->rapporti_materiali ?? [] as $item) {
                InterventEquipmentOrders::create([
                    'id_lavoro' => 0,
                    'id_intervento' => $idOperation,
                    'codice' => $item['codice'],
                    'descrizione' => $item['descrizione'],
                    'quantita' => $item['quantita']
                ]);
            }

            if($request->promemoria ?? 0) {
                $operation = $this->createOperationReminder($request, $idOperation);
            } elseif($request->stato == 3) {
                //$operation = $this->createOperation($request, $idOperation);
            }

            $this->updateMachinery(($request->intervento_macchinari ?? []), $idOperation);

            return response()->json([
                'status' => 'ok',
                'report' => $report->id_rapporto
            ], 200);
        } catch(\Throwable $e) {
            Storage::put('file.txt', $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function createOperationReminder($data, $idOperation)
    {
        $oldOperation = Operation::where('id_intervento', $idOperation)->first();
        $operation = Operation::create([
            'data' => date('Y-m-d', strtotime($oldOperation->data . " + $data->promemoria months")),
            'ora' => $oldOperation->ora,
            'urgente' => $oldOperation->urgente,
            'a_corpo' => $oldOperation->a_corpo,
            'tecnico' => $oldOperation->tecnico,
            'pronto_intervento' => $oldOperation->pronto_intervento,
            'stato' => 0,
            'note' =>'Creato da promemoria',
            'id_sede' => $oldOperation->id_sede,
            'long' => $oldOperation->long,
            'lat' => $oldOperation->lat,
            'old_id_intervento' => $idOperation,
            'incasso' => $oldOperation->incasso,
            'tipologia' => $oldOperation->tipologia,
        ]);
        foreach($data->macchinario ?? [] as $item) {
            $oldMachinery = OperationMachinery::where([['id_intervento', $idOperation], ['id_macchinario', $item['id_macchinario']]])->first();
            OperationMachinery::create([
                'id_intervento' => $operation->id_intervento,
                'id_macchinario' => $item['id_macchinario'],
                'desc_intervento' => $item['desc_intervento'] ?? '',
            ]);
        }
        return $operation->id_intervento;
    }

    public function createOperation($data, $idOperation)
    {
        $oldOperation = Operation::where('id_intervento', $idOperation)->first();
        $operation = Operation::create([
            'urgente' => $oldOperation->urgente,
            'a_corpo' => $oldOperation->a_corpo,
            'tecnico' => $oldOperation->tecnico,
            'pronto_intervento' => 1,
            'stato' => 0,
            'note' =>' Proseguo intervento del ' . date('d/m/Y', strtotime($oldOperation->data)),
            'id_sede' => $oldOperation->id_sede,
            'long' => $oldOperation->long,
            'lat' => $oldOperation->lat,
            'old_id_intervento' => $idOperation,
            'incasso' => $oldOperation->incasso,
            'tipologia' => $oldOperation->tipologia,
        ]);
        foreach($data->macchinario ?? [] as $item) {
            $oldMachinery = OperationMachinery::where([['id_intervento', $idOperation], ['id_macchinario', $item['id_macchinario']]])->first();
            OperationMachinery::create([
                'id_intervento' => $operation->id_intervento,
                'id_macchinario' => $item->machinery['id_macchinario'],
                'desc_intervento' => $item->machinery['desc_intervento'] ?? ''
            ]);
        }
        return $operation->id_intervento;
    }

    public function updateMachinery($machineries, $idOperation)
    {
        foreach($machineries as $machinery)
        {
            OperationMachinery::where([['id_intervento', $idOperation], ['id_macchinario', $machinery['id_macchinario']]])
                ->update([
                    'desc_intervento' => $machinery['desc_intervento'] ?? '',
                    'rapporto_initial' => $machinery['rapporto_initial'] ?? null,
                    'rapporto_state' => $machinery['rapporto_state'] ?? null,
                ]);
        }
    }

    private function getProgressive($request)
    {
        // $user = CoreUsers::where('app_token', $request->header('userToken'))->first();
        // $lastReport = Report::where('data_intervento', 'like', date('Y') . '%')
        //     ->whereHas('operation', function($q) use($user) {
        //         $q->where('tecnico', 'like', $user->id_user . ';%');
        //     })->orderBy('progressivo', 'desc')->first();
        $lastReport = Report::where('data_intervento', 'like', date('Y') . '%')
            ->orderBy('progressivo', 'desc')->first();
        if($lastReport) {
            return ($lastReport->progressivo + 1);
        }
        return 1;
    }
}
