<?php

namespace App\Http\Controllers;

use App\Models\Addresses\Nazioni;
use App\Models\Clienti;
use App\Models\Location;
use App\Models\Machinery;
use App\Models\Operation\OperationMachinery;
use App\Models\Operation\Operation;
use Carbon\Carbon;
use Excel;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Storage;
use App\Models\Addresses\Comuni;
use App\Models\Addresses\Province;
use App\Helpers\VatNumber;

class VatNumberController extends MainController
{
    public function index()
    {
        $chars = preg_split('//', \Request::get('permissionAttribute'), -1, PREG_SPLIT_NO_EMPTY);
        $this->data['chars'] = $chars;
        $this->data['addRoute'] = '/wrong_vat_number_add';
        $this->data['ajaxRoute'] = 'wrong_vat_number';
        return view('clienti.customers', $this->data);
    }

    public function ajax()
    {
        $this->data['pages'] = Clienti::with('location')->where('partita_iva_errata', 1)->get();
		$chars = preg_split('//', \Request::get('permissionAttribute'), -1, PREG_SPLIT_NO_EMPTY);
        if (in_array("W", $chars) && !in_array("V", $chars)) {
            $this->data['pages'] = Clienti::where('id_user', auth()->user()->id_user)->where('partita_iva_errata', 1)->get();
        } else {
            $this->data['pages'] = Clienti::where('partita_iva_errata', 1)->get()->sortBy('ragione_sociale');
        }
        $index = 0;
        $data_json = [];
        foreach ($this->data['pages'] as $items) {
            $locationFinal = '';
            $i = 0;
            $len = count($items->location);
            foreach($items->location as $location){
				
				if ($i == $len - 1) {
                    $locationFinal .= $location->tipologia;
                }
                else  {
                    $locationFinal .= $location->tipologia . ', ';
                }
                $i++;
            }
            $data_json[] = [
                "{$items->id}",
                "{$items->ragione_sociale}",
                "{$items->partita_iva}",
                "{$items->codice_fiscale}",
                "{$locationFinal}"

            ];
            if (in_array("V", $chars)) {
                array_push($data_json[$index], "<a href=\"/wrong_vat_number_add/$items->id/view\" class=\"btn btn-xs btn-primary\" title=\"Visualizza\"><i class=\"fa fa-eye\"></i></a>");
            }
            if (in_array("E", $chars)) {
                array_push($data_json[$index], "<a href=\"/wrong_vat_number_add/$items->id\" class=\"btn btn-xs btn-info\" title=\"Modifica\"><i class=\"fa fa-edit\"></i></a>");
            }
            if (in_array("L", $chars)) {
                $lock_btn = ($items->cliente_visibile == 1) ? 'warning' : 'primary';
                $lock_icon = ($items->cliente_visibile == 1) ? 'lock' : 'unlock';
                $title_attr = ($items->cliente_visibile == 1) ? 'Nascondi' : 'Mostra';
                array_push($data_json[$index], "<button onclick='lockBlock(this)' title=\"$title_attr\"  data-my-id=\"$items->id\" data-my-current=\"$items->cliente_visibile\"  type=\"button\" class=\"action_block btn btn-xs btn-$lock_btn\" ><i class=\"fa fa-$lock_icon\"></i></button>");
            }
           
            if (in_array("D", $chars)) {
                array_push($data_json[$index], "<button onclick='deleteBlock(this)' data-my-id=\"$items->id\" type=\"button\" class=\"action_del btn btn-xs btn-warning\" title=\"Elimina\"><i class=\"fa fa-trash\"></i></button>");
            }
            $index++;
        }

        return response()->json([
            'draw' => 1,
            'recordsTotal' => $this->data['pages']->count(),
            "data" => $data_json,
        ]);
    }

    public function add($id = null, Request $request)
    {
        $this->data['page'] = [];
        $this->data['nazionie'] = Nazioni::all();
        $this->data['chars'] = preg_split('//', \Request::get('permissionAttribute'), -1, PREG_SPLIT_NO_EMPTY);

        if (Input::get('save')) {
            return $this->save($id, $request);
        }

        if (!empty($id)) {
            $this->data['page'] = Clienti::where('id', $id)->with('location.machineries')->first();
        }
        $this->data['backRoute'] = $request->backRoute ?? '/wrong_vat_number';
        return view('clienti.wrong_vat_number_add', $this->data);
    }

    public function save($id = null, $request)
    {
        $validator = \Validator::make($request->all(), [
            'ragione_sociale' => 'required|string',
        ]);
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator->errors());
        }
        $data = [
            'ragione_sociale' => $request->ragione_sociale,
            'partita_iva' => $request->partita_iva,
            'codice_fiscale' => $request->codice_fiscale,
            'note' => $request->note,
            'partita_iva_errata' => VatNumber::isNotValid($request->partita_iva)
        ];

        if (empty($id)) {
            $createdClient = Clienti::create($data);
            Session::flash('success', 'Il cliente è aggiunto!');
            $id = $createdClient->id;
        } else {
            Clienti::where('id', $id)->update($data);
            Session::flash('success', 'Le modifiche sono state salvate correttamente!');
        }
        return redirect("/wrong_vat_number");
    }


    public function block()
    {
        $id = Input::get('id');
        $block = (int)Input::get('block');
        if (!empty($id)) {
            $data = [
                'cliente_visibile' => ($block != 1) ? 1 : 0
            ];
            Clienti::where('id', $id)->update($data);
        }
        return response()->json(array('statut' => 'Success'), 200);
    }

    public function delete()
    {
        $id = Input::get('id');
        if (!empty($id)) {
            $getClient = Clienti::where('id', $id)->with('location')->first();
            if($getClient){
                foreach($getClient->location as $location){
                    if($location){
                        $machinery = Machinery::where('id_sedi', $location->id_sedi)->get();
                        foreach($machinery as $m){
                            $getOperationMachinery = OperationMachinery::where('id_macchinario', $m->id_macchinario)->get();
                            if($getOperationMachinery){
                                foreach($getOperationMachinery as $operationMachinery){
                                    Operation::where('id_intervento', $operationMachinery->id_intervento)->delete();
                                }
                            }
                        }
                        Machinery::where('id_sedi', $location->id_sedi)->delete();
                    }
                }
                Location::where('id_cliente', $id)->delete();
            }
            Clienti::where('id', $id)->delete();
        }
        return response()->json(array('statut' => 'Success'), 200);
    }

    public function search($id, Request $request)
    {
        $data = [];
        if ($id == 1) {
            $clienti = Clienti::where('ragione_sociale', 'like', '%' . $request->value . '%')->get();
            foreach ($clienti as $c) {
                $data[] = [
                    'data' => $c->ragione_sociale
                ];
            }
            return response()->json(['result' => $data]);

        } elseif ($id == 2) {
            $clienti = Clienti::where('partita_iva', 'like', $request->value . '%')->get();
            foreach ($clienti as $c) {
                $data[] = [
                    'data' => $c->partita_iva
                ];
            }
            return response()->json(['result' => $data]);
        } elseif ($id == 3) {
            $clienti = Clienti::where('codice_fiscale', 'like', $request->value . '%')->get();
            foreach ($clienti as $c) {
                $data[] = [
                    'data' => $c->codice_fiscale
                ];
            }
            $unique = collect($data)->unique('data');
            return response()->json(['result' => $unique->values()->all()]);
        } else {
            return response()->json(['app' => 'error'], 400);
        }
        return $id;
    }

    protected function replaceDiacriticeChar($word)
    {
        if (strlen($word) === 0) {
            return $word;
        }
        $diacriticeChar = ['ă', 'â', 'î', 'ş', 'ţ'];
        $withoutDiacriticeChar = ['a', 'a', 'i', 's', 't'];
        return strtolower(str_replace($diacriticeChar, $withoutDiacriticeChar, $word));
    }

    public function download(Request $request)
    {
        $clienti = Clienti::whereIn('id', explode(",", $request->ids))->get();
        foreach ($clienti as $page) {
            $dataArray[] = [
                'Cod. cliente' => $page->id,
                'Denominazione' => $page->ragione_sociale,
                'Partita IVA' => $page->partita_iva,
                'Codice Fiscale' => $page->codice_fiscale,
                'Visibile' => ($page->cliente_visibile == 1) ? 'Si' : 'No'
            ];
        }
        $exp = Excel::import(function ($excel) use ($dataArray) {
            $excel->setTitle('Clienti table');
            $excel->sheet('clienti', function ($sheet) use ($dataArray) {
                $sheet->cell('A1', function ($cell) {
                    $cell->setValue('Cod. cliente');
                });
                $sheet->cell('B1', function ($cell) {
                    $cell->setValue('Denominazione');
                });
                $sheet->cell('C1', function ($cell) {
                    $cell->setValue('Partita IVA');
                });
                $sheet->cell('D1', function ($cell) {
                    $cell->setValue('Codice Fiscale');
                });
                $sheet->cell('E1', function ($cell) {
                    $cell->setValue('Visibile');
                });
                $sheet->cells('A1:E1', function ($cells) {
                    $cells->setFontWeight('bold');
                    $cells->setBorder('solid', 'solid', 'solid', 'solid');
                });
                $sheet->fromArray($dataArray, null, 'A2', false, false);
            });
        });
        $file = storage_path('exports/' . $exp['file']);
        return response()->download($file)->deleteFileAfterSend(true);
    }

    public function getComuneNascita (Request $request){
        $r = $request['params'];
        $value = strtoupper($r['value']);
        $comuni = Comuni::where('comune', 'like', '%' . $value . '%')->get();
        $comuni = $comuni->load('provice');

        return response()->json(['comuni' => $comuni]);
    }

    public function getComuneNascitaByCap (Request $request) {
        $r = $request['params'];
        $value = strtoupper($r['value']);
        if(isset($r['comune_sl'])){
            $comune_sl = $r['comune_sl'];
            $comuni = Comuni::where('cap', 'like', '%' . $value . '%')->where('comune', 'like', '%' . $comune_sl. '%')->get();
        }else{
            $comuni = Comuni::where('cap', 'like', '%' . $value . '%')->get();
        }
        
        $comuni = $comuni->load('provice');

        return response()->json(['comuni' => $comuni]);
    }
}

