<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\Machinery;
use App\Models\Clienti;
use App\Http\Requests\MachineryRequest;

class MachineryController extends Controller
{
    public function add(Request $request, $id)
    {
        $this->data['chars'] = preg_split('//', \Request::get('permissionAttribute'), -1, PREG_SPLIT_NO_EMPTY);
        $this->data['id_location'] = $id;
        $this->data['location'] = Location::where('id_sedi', $id)->first();
        $this->data['backRoute'] = $request->backRoute ?? '/customers';
        return view('machinery.machinery_add', $this->data);
    }

    public function edit(Request $request, $id)
    {
        $this->data['chars'] = preg_split('//', \Request::get('permissionAttribute'), -1, PREG_SPLIT_NO_EMPTY);
        $this->data['machinery'] = Machinery::where('id_macchinario', $id)->with('location')->first();
        $this->data['client'] = Clienti::where('id', $this->data['machinery']['location']['id_cliente'])->first();
        $this->data['backRoute'] = $request->backRoute ?? '/customers';
        return view('machinery.machinery_edit', $this->data);
    }

    public function view(Request $request, $id)
    {
        $this->data['chars'] = preg_split('//', \Request::get('permissionAttribute'), -1, PREG_SPLIT_NO_EMPTY);
        $this->data['location'] = Location::where('id_sedi', $id)->with('client')->first();
        $this->data['machinery'] = Machinery::where('id_macchinario', $id)->with('location')->first();
        $this->data['client'] = Clienti::where('id', $this->data['machinery']['location']['id_cliente'])->first();
        $this->data['backRoute'] = $request->backRoute ?? '/customers';
        return view('machinery.machinery_view', $this->data);
    }

    public function update(MachineryRequest $request, $id_machinery)
    {

      $id = $id_machinery;

      $machinery = Machinery::where('id_macchinario', $id)->first();
      $data = [
          //'id_sedi' => $id,
          'descrizione' => $request->descrizione ?? null,
          'anno' => $request->anno ?? null,
          // 'note' => $request->note ?? null,
          'tipologia' => $request->tipologia,
      ];
      if($machinery->alldata) unset($data['tipologia']);
      switch($request->tipologia){
        case "Caldo":
          $data = array_merge([
            'tetto' => $request->tetto? 1 : 0,
            '2tecnici' => $request['2tecnici']? 1 : 0,
            'tipo_impianto' => $request->tipo_impianto ?? null,
            "C_costruttore" => $request->C_costruttore ?? null,
            "modello" => $request->modello ?? null,
            //"C_matr_anno" => $request->C_matr_anno ?? null,
            "matricola" => $request->matricola ?? null,
            "C_nominale" => $request->C_nominale ?? 0,
            "C_combustibile" => $request->C_combustibile ?? null,
            "C_tiraggio" => $request->C_tiraggio ?? null,
            "C_uscitafumi" => $request->C_uscitafumi ?? 0,
            "C_libretto" => $request->C_libretto ?? 0,
            "C_LA_locale" => $request->C_LA_locale ?? null,
            "C_LA_idoneo" => $request->C_LA_idoneo ?? 0,
            "C_LA_presa_aria" => $request->C_LA_presa_aria ?? 0,
            "C_LA_presa_aria_idonea" => $request->C_LA_presa_aria_idonea ?? 0,
            "C_KRA_dimensioni" => $request->C_KRA_dimensioni ?? null,
            "C_KRA_materiale" => $request->C_KRA_materiale ?? null,
            "C_KRA_coibenza" => $request->C_KRA_coibenza ?? null,
            "C_KRA_curve90" => $request->C_KRA_curve90 ?? null,
            "C_KRA_lunghezza" => $request->C_KRA_lunghezza ?? 0,
            "C_KRA_idoneo" => $request->C_KRA_idoneo ?? 0,
            "C_CA_tipo" => $request->C_CA_tipo ?? null,
            "C_CA_materiale" => $request->C_CA_materiale ?? null,
            "C_CA_sezione" => $request->C_CA_sezione ?? null,
            "C_CA_dimensioni" => $request->C_CA_dimensioni ?? null,
            "C_CA_lunghezza" => $request->C_CA_lunghezza ?? 0,
            "C_CA_cam_raccolta" => $request->C_CA_cam_raccolta ?? 0,
            "C_CA_cam_raccolta_ispez" => $request->C_CA_cam_raccolta_ispez ?? 0,
            "C_CA_curve90" => $request->C_CA_curve90 ?? null,
            "C_CA_curve45" => $request->C_CA_curve45 ?? null,
            "C_CA_curve15" => $request->C_CA_curve15 ?? null,
            "C_CA_piombo" => $request->C_CA_piombo ?? 0,
            "C_CA_liberaindipendente" => $request->C_CA_liberaindipendente ?? 0,
            "C_CA_innesti" => $request->C_CA_innesti ?? 0,
            "C_CA_rotture" => $request->C_CA_rotture ?? 0,
            "C_CA_occlusioni" => $request->C_CA_occlusioni ?? 0,
            "C_CA_corpi_estranei" => $request->C_CA_corpi_estranei ?? 0,
            "C_CA_cambiosezione" => $request->C_CA_cambiosezione ?? 0,
            "C_CA_restringe" => $request->C_CA_restringe ?? 0,
            "C_CA_diventa" => $request->C_CA_diventa ?? null,
            "C_CA_provatiraggio" => $request->C_CA_provatiraggio ?? null,
            "C_CA_tiraggio" => $request->C_CA_tiraggio ?? null,
            "C_CA_tettolegno" => $request->C_CA_tettolegno ?? 0,
            "C_CA_distanze_sicurezza" => $request->C_CA_distanze_sicurezza ?? null,
            "C_CA_certificazione" => $request->C_CA_certificazione ?? 0,
            "C_KCO_dimensioni" => $request->C_KCO_dimensioni ?? null,
            "C_KCO_forma" => $request->C_KCO_forma ?? null,
            "C_KCO_cappelloterminale" => $request->C_KCO_cappelloterminale ?? null,
            "C_KCO_zonareflusso" => $request->C_KCO_zonareflusso ?? 0,
            "C_KCO_graditetto" => $request->C_KCO_graditetto ?? null,
            "C_KCO_accessotetto" => $request->C_KCO_accessotetto ?? null,
            "C_KCO_comignolo" => $request->C_KCO_comignolo ?? 0,
            "C_KCO_tipocomignolo" => $request->C_KCO_tipocomignolo ?? null,
            "C_KCO_idoncomignolo" => $request->C_KCO_idoncomignolo ?? 0,
            "C_KCO_cestello" => $request->C_KCO_cestello ?? 0,
            "C_KCO_ponteggio" => $request->C_KCO_ponteggio ?? 0,
          ], $data);
          break;
        case "Freddo":
          $data = array_merge([
            "modello" => $request->modello ?? null,
            "matricola" => $request->matricola ?? null,
            "F_anno_aquisto" => $request->F_anno_aquisto ?? null,
            "F_tipo_gas" => $request->F_tipo_gas ?? null,
            'tetto' => $request->tetto? 1 : 0,
            '2tecnici' => $request['2tecnici']? 1 : 0,
          ],$data);
          break;
        case "Sopralluogo Caldo":
          $data = array_merge([
            "tipo_impianto" => $request->tipo_impianto ?? null,
            "SC_posizione_cana" => $request->SC_posizione_cana ?? null,
            "SC_certif_canna" => $request->SC_certif_canna ?? 0,
            "SC_cana_da_intubare" => $request->SC_cana_da_intubare ?? 0,
            "SC_metri" => $request->SC_metri ?? 0,
            "SC_curve" => $request->SC_curve ?? 0,
            "SC_materiale" => $request->SC_materiale ?? null,
            "SC_ind_com" => $request->SC_ind_com ?? 0,
            "SC_tondo_oval" => $request->SC_tondo_oval ?? null,
            "SC_sezione" => $request->SC_sezione ?? 0,
            "SC_tetto_legno" => $request->SC_tetto_legno ?? 0,
            "SC_distanze" => $request->SC_distanze ?? null,
          ],$data);
          break;
        case "Sopralluogo Freddo":
          $data = array_merge([
            "SF_split" => $request->SF_split ?? null,
            "SF_canalizzato" => $request->SF_canalizzato ?? null,
            "SF_predisp_presente" => $request->SF_predisp_presente ?? 0,
            "SF_imp_el_presente" => $request->SF_imp_el_presente ?? 0,
            "SF_mq_locali" => $request->SF_mq_locali ?? 0,
            "SF_altezza" => $request->SF_altezza ?? 0,
            "SF_civile" => $request->SF_civile ?? 0,
            "SF_indust_commer" => $request->SF_indust_commer ?? 0,
          ],$data);
          break;
      }

      $data['alldata'] = 1;
      foreach ($data as $value) {
          if ($value == null) {
              $data['alldata'] = 0;
          }
      }

        Machinery::where('id_macchinario', $id)->update($data);

        $updatedMachinery = Machinery::where('id_macchinario', $id)->with(['location' => function($query){
          return $query->with('client');
        }])->first();

        return redirect()->to('/customer_add/'. $updatedMachinery->location->client->id. "?backRoute=". $request->backRoute ?? '/customers');
    }

    public function save(MachineryRequest $request, $id)
    {
        $data = [
            'id_sedi' => $id,
            'descrizione' => $request->descrizione ?? null,
            'anno' => $request->anno ?? null,
            // 'note' => $request->note ?? null,
            'tipologia' => $request->tipologia,
        ];
        switch($request->tipologia){
          case "Caldo":
            $data = array_merge([
              'tetto' => $request->tetto? 1 : 0,
              '2tecnici' => $request['2tecnici']? 1 : 0,
              'tipo_impianto' => $request->tipo_impianto ?? null,
              "C_costruttore" => $request->C_costruttore ?? null,
              "modello" => $request->modello ?? null,
              // "C_matr_anno" => $request->C_matr_anno ?? null,
              "matricola" => $request->matricola ?? null,
              "C_nominale" => $request->C_nominale ?? 0,
              "C_combustibile" => $request->C_combustibile ?? null,
              "C_tiraggio" => $request->C_tiraggio ?? null,
              "C_uscitafumi" => $request->C_uscitafumi ?? 0,
              "C_libretto" => $request->C_libretto ?? 0,
              "C_LA_locale" => $request->C_LA_locale ?? null,
              "C_LA_idoneo" => $request->C_LA_idoneo ?? 0,
              "C_LA_presa_aria" => $request->C_LA_presa_aria ?? 0,
              "C_LA_presa_aria_idonea" => $request->C_LA_presa_aria_idonea ?? 0,
              "C_KRA_dimensioni" => $request->C_KRA_dimensioni ?? null,
              "C_KRA_materiale" => $request->C_KRA_materiale ?? null,
              "C_KRA_coibenza" => $request->C_KRA_coibenza ?? null,
              "C_KRA_curve90" => $request->C_KRA_curve90 ?? null,
              "C_KRA_lunghezza" => $request->C_KRA_lunghezza ?? 0,
              "C_KRA_idoneo" => $request->C_KRA_idoneo ?? 0,
              "C_CA_tipo" => $request->C_CA_tipo ?? null,
              "C_CA_materiale" => $request->C_CA_materiale ?? null,
              "C_CA_sezione" => $request->C_CA_sezione ?? null,
              "C_CA_dimensioni" => $request->C_CA_dimensioni ?? null,
              "C_CA_lunghezza" => $request->C_CA_lunghezza ?? 0,
              "C_CA_cam_raccolta" => $request->C_CA_cam_raccolta ?? 0,
              "C_CA_cam_raccolta_ispez" => $request->C_CA_cam_raccolta_ispez ?? 0,
              "C_CA_curve90" => $request->C_CA_curve90 ?? null,
              "C_CA_curve45" => $request->C_CA_curve45 ?? null,
              "C_CA_curve15" => $request->C_CA_curve15 ?? null,
              "C_CA_piombo" => $request->C_CA_piombo ?? 0,
              "C_CA_liberaindipendente" => $request->C_CA_liberaindipendente ?? 0,
              "C_CA_innesti" => $request->C_CA_innesti ?? 0,
              "C_CA_rotture" => $request->C_CA_rotture ?? 0,
              "C_CA_occlusioni" => $request->C_CA_occlusioni ?? 0,
              "C_CA_corpi_estranei" => $request->C_CA_corpi_estranei ?? 0,
              "C_CA_cambiosezione" => $request->C_CA_cambiosezione ?? 0,
              "C_CA_restringe" => $request->C_CA_restringe ?? 0,
              "C_CA_diventa" => $request->C_CA_diventa ?? null,
              "C_CA_provatiraggio" => $request->C_CA_provatiraggio ?? null,
              "C_CA_tiraggio" => $request->C_CA_tiraggio ?? null,
              "C_CA_tettolegno" => $request->C_CA_tettolegno ?? 0,
              "C_CA_distanze_sicurezza" => $request->C_CA_distanze_sicurezza ?? null,
              "C_CA_certificazione" => $request->C_CA_certificazione ?? 0,
              "C_KCO_dimensioni" => $request->C_KCO_dimensioni ?? null,
              "C_KCO_forma" => $request->C_KCO_forma ?? null,
              "C_KCO_cappelloterminale" => $request->C_KCO_cappelloterminale ?? null,
              "C_KCO_zonareflusso" => $request->C_KCO_zonareflusso ?? 0,
              "C_KCO_graditetto" => $request->C_KCO_graditetto ?? null,
              "C_KCO_accessotetto" => $request->C_KCO_accessotetto ?? null,
              "C_KCO_comignolo" => $request->C_KCO_comignolo ?? 0,
              "C_KCO_tipocomignolo" => $request->C_KCO_tipocomignolo ?? null,
              "C_KCO_idoncomignolo" => $request->C_KCO_idoncomignolo ?? 0,
              "C_KCO_cestello" => $request->C_KCO_cestello ?? 0,
              "C_KCO_ponteggio" => $request->C_KCO_ponteggio ?? 0,
            ], $data);
            break;
          case "Freddo":
            $data = array_merge([
              "modello" => $request->modello ?? null,
              "matricola" => $request->matricola ?? null,
              "F_anno_aquisto" => $request->F_anno_aquisto ?? null,
              "F_tipo_gas" => $request->F_tipo_gas ?? null,
              'tetto' => $request->tetto? 1 : 0,
              '2tecnici' => $request['2tecnici']? 1 : 0,
            ],$data);
            break;
          case "Sopralluogo Caldo":
            $data = array_merge([
              "tipo_impianto" => $request->tipo_impianto ?? null,
              "SC_posizione_cana" => $request->SC_posizione_cana ?? null,
              "SC_certif_canna" => $request->SC_certif_canna ?? 0,
              "SC_cana_da_intubare" => $request->SC_cana_da_intubare ?? 0,
              "SC_metri" => $request->SC_metri ?? 0,
              "SC_curve" => $request->SC_curve ?? 0,
              "SC_materiale" => $request->SC_materiale ?? null,
              "SC_ind_com" => $request->SC_ind_com ?? 0,
              "SC_tondo_oval" => $request->SC_tondo_oval ?? null,
              "SC_sezione" => $request->SC_sezione ?? 0,
              "SC_tetto_legno" => $request->SC_tetto_legno ?? 0,
              "SC_distanze" => $request->SC_distanze ?? null,
            ],$data);
            break;
          case "Sopralluogo Freddo":
            $data = array_merge([
              "SF_split" => $request->SF_split ?? null,
              "SF_canalizzato" => $request->SF_canalizzato ?? null,
              "SF_predisp_presente" => $request->SF_predisp_presente ?? 0,
              "SF_imp_el_presente" => $request->SF_imp_el_presente ?? 0,
              "SF_mq_locali" => $request->SF_mq_locali ?? 0,
              "SF_altezza" => $request->SF_altezza ?? 0,
              "SF_civile" => $request->SF_civile ?? 0,
              "SF_indust_commer" => $request->SF_indust_commer ?? 0,
            ],$data);
            break;
        }

        $data['alldata'] = 1;
        foreach ($data as $value) {
            if ($value == null) {
                $data['alldata'] = 0;
            }
        }

        $newMachinery = Machinery::create($data);
        $location = Location::where('id_sedi', $id)->with('client')->first();
        return redirect()->to('/customer_add/'. $location->id_cliente. "?backRoute=". $request->backRoute ?? '/customers');
    }

    public function delete($id)
    {
        $getMachinery = Machinery::where('id_macchinario', $id)->first();
        if($getMachinery){
            $getMachinery->delete();
        }
        return response()->json(array('statut' => 'Success'), 200);
    }

    public function lock(Request $request, $id)
    {
        if($id) {
            $data = [
                'attivo' => ($request->status != 1) ? 1 : 0
            ];
            Machinery::where('id_macchinario', $id)->update($data);
        }
        return response()->json(array('statut' => 'Success'), 200);
    }

    public function getTypes(){
        $types = [
            'Sede legale',
            'Sede amministrativa',
            'Sede operativa',
            'Magazzino',
            'Altro'
        ];
        return $types;
    }
}
