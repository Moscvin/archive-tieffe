<?php

namespace App\Http\Controllers\Api\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CoreUsers;
use App\Models\Operation\Operation;
use Illuminate\Support\Facades\URL;

class GetController extends Controller
{
    public function main(Request $request, $id)
    {
        try {
            $user = CoreUsers::where('app_token', $request->header('userToken'))->first();
            $operation = Operation::where('id_intervento', $id)->with(['invoice_client', 'headquarter.client', 'machineries.machinery'])->first();
            $response = [
                'id_intervento' => $operation->id_intervento,
                'data' => $operation->data,
                'tipologia' => $operation->tipologia,
                'ora_dalle' => $operation->ora_dalle,
                'ora_alle' => $operation->ora_alle,
                'file' => $operation->file ? URL::to('/') . '/file/' . $operation->file : null,
                'cliente' => (object)[
                    'cliente_id' => $operation->headquarter->client->id ?? 0,
                    'cliente_denominazione' => $operation->headquarter->client->ragione_sociale ?? '',
                    'note' => $operation->headquarter->client->note ?? '',
                    'alldata_cliente' => !(empty($operation->headquarter->client->partita_iva) &&
                    empty($operation->headquarter->client->codice_fiscale)),
                    'cliente_codice_fiscale' => $operation->headquarter->client->codice_fiscale ?? '',
                    'cliente_partita_iva' => $operation->headquarter->client->partita_iva ?? '',
                ],
                'sede' => (object)[
                    'id_sede' => $operation->headquarter->id_sedi ?? 0,
                    'telefono1' => $operation->headquarter->telefono1 ?? '',
                    'telefono2' => $operation->headquarter->telefono2 ?? '',
                    'denominazione' => $operation->headquarter->address ?? '',
                    'email' => $operation->headquarter->email ?? '',
                    'lat' => $operation->lat,
                    'long' => $operation->long,
                    'indirizzo' => (object) [
                      'indirizzo_via' => $operation->headquarter->indirizzo_via,
                      'indirizzo_civico' => $operation->headquarter->indirizzo_civico,
                      'indirizzo_cap' => $operation->headquarter->indirizzo_cap,
                      'indirizzo_comune' => $operation->headquarter->indirizzo_comune,
                      'indirizzo_provincia' => $operation->headquarter->indirizzo_provincia,
                    ],
                    'tipologia' => $operation->headquarter->tipologia,
                    'alldata_sede' => false,
                ],
                'descrizione_intervento' => $operation->machineries->implode('desc_intervento', ', ') ?? '',
                'star' => $this->isResponsible($user->id_user, $operation->techniciansArray) ? 1 : 0,
                'stato' => $operation->stato,
                'urgente' => $operation->urgente ?? 0,
                'updated_at' => (string)$operation->updated_at,
                'non_assegnati' => $operation->pronto_intervento,
                'fatturare_a' => $operation->fatturare_a > 0 ? $operation->invoice_client->ragione_sociale : 'Cliente',
                'note' => $operation->note,
                'rapporto' => $operation->report->id_rapporto ?? 0,
                'technicians' => $operation->technicians()->map(function($item) {
                    return (object)[
                        'id_user' => $item->id_user,
                        'name' => $item->fullName
                    ];
                }),
                'incasso' => $operation->incasso
            ];

            if($operation->machineries->count() > 0){
               $response['machineries'] = [
                   'id_macchinario' => $operation->machineries->first()->id_macchinario,
                   'descrizione' => $operation->machineries->first()->machinery->descrizione,
                   'modello' => $operation->machineries->first()->machinery->modello,
                   'matricola' => $operation->machineries->first()->machinery->matricola,
                   'anno' => $operation->machineries->first()->machinery->anno,
                   'note' => $operation->machineries->first()->machinery->note,
                   'attivo' => $operation->machineries->first()->machinery->attivo,
                   'tetto' => $operation->machineries->first()->machinery->tetto,
                   '2tecnici' => $operation->machineries->first()->machinery['2tecnici'],
                   'SF_split' => $operation->machineries->first()->machinery->SF_split,
                   'SF_canalizzato' => $operation->machineries->first()->machinery->SF_canalizzato,
                   'SF_predisp_presente' => $operation->machineries->first()->machinery->SF_predisp_presente,
                   'SF_imp_el_presente' => $operation->machineries->first()->machinery->SF_imp_el_presente,
                   'SF_mq_locali' => $operation->machineries->first()->machinery->SF_mq_locali,
                   'SF_altezza' => $operation->machineries->first()->machinery->SF_altezza,
                   'SF_civile' => $operation->machineries->first()->machinery->SF_civile,
                   'SF_indust_commer' => $operation->machineries->first()->machinery->SF_indust_commer,
                   'SC_posizione_cana' => $operation->machineries->first()->machinery->SC_posizione_cana,
                   'SC_certif_canna' => $operation->machineries->first()->machinery->SC_certif_canna,
                   'SC_cana_da_intubare' => $operation->machineries->first()->machinery->SC_cana_da_intubare,
                   'SC_metri' => $operation->machineries->first()->machinery->SC_metri,
                   'SC_materiale' => $operation->machineries->first()->machinery->SC_materiale,
                   'SC_ind_com' => $operation->machineries->first()->machinery->SC_ind_com,
                   'SC_tondo_oval' => $operation->machineries->first()->machinery->SC_tondo_oval,
                   'SC_sezione' => $operation->machineries->first()->machinery->SC_sezione,
                   'SC_tetto_legno' => $operation->machineries->first()->machinery->SC_tetto_legno,
                   'SC_distanze' => $operation->machineries->first()->machinery->SC_distanze,
                   'F_anno_aquisto' => $operation->machineries->first()->machinery->F_anno_aquisto,
                   'F_tipo_gas' => $operation->machineries->first()->machinery->F_tipo_gas,
                   'C_costruttore' => $operation->machineries->first()->machinery->C_costruttore,
                   'C_matr_anno' => $operation->machineries->first()->machinery->C_matr_anno,
                   'C_nominale' => $operation->machineries->first()->machinery->C_nominale,
                   'C_combustibile' => $operation->machineries->first()->machinery->C_combustibile,
                   'C_tiraggio' => $operation->machineries->first()->machinery->C_tiraggio,
                   'C_uscitafumi' => $operation->machineries->first()->machinery->C_uscitafumi,
                   'C_libretto' => $operation->machineries->first()->machinery->C_libretto,
                   'C_LA_locale' => $operation->machineries->first()->machinery->C_LA_locale,
                   'C_LA_idoneo' => $operation->machineries->first()->machinery->C_LA_idoneo,
                   'C_LA_presa_aria' => $operation->machineries->first()->machinery->C_LA_presa_aria,
                   'C_LA_presa_aria_idonea' => $operation->machineries->first()->machinery->C_LA_presa_aria_idonea,
                   'C_KRA_dimensioni' => $operation->machineries->first()->machinery->C_KRA_dimensioni,
                   'C_KRA_materiale' => $operation->machineries->first()->machinery->C_KRA_materiale,
                   'C_KRA_coibenza' => $operation->machineries->first()->machinery->C_KRA_coibenza,
                   'C_KRA_curve90' => $operation->machineries->first()->machinery->C_KRA_curve90,
                   'C_KRA_lunghezza' => $operation->machineries->first()->machinery->C_KRA_lunghezza,
                   'C_KRA_idoneo' => $operation->machineries->first()->machinery->C_KRA_idoneo,
                   'C_CA_tipo' => $operation->machineries->first()->machinery->C_CA_tipo,
                   'C_CA_materiale' => $operation->machineries->first()->machinery->C_CA_materiale,
                   'C_CA_sezione' => $operation->machineries->first()->machinery->C_CA_sezione,
                   'C_CA_dimensioni' => $operation->machineries->first()->machinery->C_CA_dimensioni,
                   'C_CA_lunghezza' => $operation->machineries->first()->machinery->C_CA_lunghezza,
                   'C_CA_cam_raccolta' => $operation->machineries->first()->machinery->C_CA_cam_raccolta,
                   'C_CA_cam_raccolta_ispez' => $operation->machineries->first()->machinery->C_CA_cam_raccolta_ispez,
                   'C_CA_curve90' => $operation->machineries->first()->machinery->C_CA_curve90,
                   'C_CA_curve45' => $operation->machineries->first()->machinery->C_CA_curve45,
                   'C_CA_curve15' => $operation->machineries->first()->machinery->C_CA_curve15,
                   'C_CA_piombo' => $operation->machineries->first()->machinery->C_CA_piombo,
                   'C_CA_liberaindipendente' => $operation->machineries->first()->machinery->C_CA_liberaindipendente,
                   'C_CA_innesti' => $operation->machineries->first()->machinery->C_CA_innesti,
                   'C_CA_rotture' => $operation->machineries->first()->machinery->C_CA_rotture,
                   'C_CA_occlusioni' => $operation->machineries->first()->machinery->C_CA_occlusioni,
                   'C_CA_corpi_estranei' => $operation->machineries->first()->machinery->C_CA_corpi_estranei,
                   'C_CA_cambiosezione' => $operation->machineries->first()->machinery->C_CA_cambiosezione,
                   'C_CA_restringe' => $operation->machineries->first()->machinery->C_CA_restringe,
                   'C_CA_diventa' => $operation->machineries->first()->machinery->C_CA_diventa,
                   'C_CA_provatiraggio' => $operation->machineries->first()->machinery->C_CA_provatiraggio,
                   'C_CA_tiraggio' => $operation->machineries->first()->machinery->C_CA_tiraggio,
                   'C_CA_tettolegno' => $operation->machineries->first()->machinery->C_CA_tettolegno,
                   'C_CA_distanze_sicurezza' => $operation->machineries->first()->machinery->C_CA_distanze_sicurezza,
                   'C_CA_certificazione' => $operation->machineries->first()->machinery->C_CA_certificazione,
                   'C_KCO_dimensioni' => $operation->machineries->first()->machinery->C_KCO_dimensioni,
                   'C_KCO_forma' => $operation->machineries->first()->machinery->C_KCO_forma,
                   'C_KCO_cappelloterminale' => $operation->machineries->first()->machinery->C_KCO_cappelloterminale,
                   'C_KCO_zonareflusso' => $operation->machineries->first()->machinery->C_KCO_zonareflusso,
                   'C_KCO_graditetto' => $operation->machineries->first()->machinery->C_KCO_graditetto,
                   'C_KCO_accessotetto' => $operation->machineries->first()->machinery->C_KCO_accessotetto,
                   'C_KCO_comignolo' => $operation->machineries->first()->machinery->C_KCO_comignolo,
                   'C_KCO_tipocomignolo' => $operation->machineries->first()->machinery->C_KCO_tipocomignolo,
                   'C_KCO_idoncomignolo' => $operation->machineries->first()->machinery->C_KCO_idoncomignolo,
                   'C_KCO_cestello' => $operation->machineries->first()->machinery->C_KCO_cestello,
                   'C_KCO_ponteggio' => $operation->machineries->first()->machinery->C_KCO_ponteggio,
                   'tipo_impianto' => $operation->machineries->first()->machinery->tipo_impianto,
               ];
               $response['machineries']['alldata_macchinario'] = $operation->machineries->first()->machinery->alldata;
            }
            $responseSede = $response['sede'];
            $response['sede']->alldata_sede = true;
            unset($responseSede->telefono2,$responseSede->email, $responseSede->note);
            foreach ($responseSede as $value) {
                if ($value == null) {
                    $response['sede']->alldata_sede = false;
                }
            }

            return response()->json([
                'status' => 'ok',
                'data' => $response
            ], 200);
        } catch(\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'trace' => $e->getTrace(),
            ], 500);
        }
    }

    private function isResponsible($idUser, $techniciansArray) {
        return $idUser == ($techniciansArray[0] ?? 0);
    }

    private function alldata($data){
      foreach ($data as $value) {
          if ($value == null || $value == '') {
              return false;
          }
      }
      return true;
    }
}
