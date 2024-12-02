<?php

namespace App\Http\Controllers;
use App\Models\Clienti;
use App\Models\Macchinari;
use App\Models\Interventi;
use Carbon\Carbon;
use Excel;
use File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Storage;
use Illuminate\Http\Request;

class MacchinariController extends Controller
{
    public function index (){

        $chars = preg_split('//', \Request::get('permissionAttribute'), -1, PREG_SPLIT_NO_EMPTY);
        
        $this->data['chars'] = $chars;

        //$this->data['clients'] = Clienti::get();
        return view('macchinari.macchinari', $this->data);

    }

    public function search(Request $request){

        $r = $request['params'];
        $value = $r['value'];


        $clienti = Clienti::where('ragione_sociale', 'like', '%' . $value . '%')->orWhere('cognome', 'like', '%' . $value . '%')->orWhere('nome', 'like', '%' . $value . '%')->get();
            if($clienti->count() > 0){
                foreach ($clienti as $c) {
                    if ($c->azienda == 1) {
                        $name = $c->ragione_sociale;
                        $data[] = [
                            'name' => str_limit($name,35,'...'),
                            'id' => $c->id
                        ];
                    } else {
                        $name = $c->cognome . ' ' . $c->nome;
                        $data[] = [
                            'name' => str_limit($name,35,'...'),
                            'id' => $c->id
                        ];
                    }
                }
            }else{
                $data[] = []; 
            }
            return response()->json(['clienti' => $data]);
    }

    public function download(Request $request)
    {

        $macchinaris = Macchinari::whereIn('id_macchinario', explode(",", $request->ids))->get();
        $macchinaris->load('cliente');


        foreach ($macchinaris as $page) {
            
            $cliente_nane = $page->cliente->azienda == 1 ? $page->cliente->ragione_sociale : $page->cliente->cognome . ' ' . $page->cliente->nome;
            $marca = $page->marca;
            $attrezzatura = $page->attrezzatura;
            $verifica_periodica = $page->verifica_periodica == 1 ? 'Si ('.$page->periodicita_verifica_mesi.' mesi)' : 'No';
            $data_ultima_verifica = !$page->data_ultima_verifica ? ' ' : Carbon::createFromFormat('d/m/Y',$page->data_ultima_verifica);


            $dataArray[] = [
                'Cliente' => $cliente_nane,
                'Marca' => $marca,
                'Attrezzatura' => $attrezzatura,
                'Verifica Periodica' => $verifica_periodica,
                'Ultima verifica' => $data_ultima_verifica,
                
            ];
        }

        $exp = Excel::create('MacchinariDatabase' . Carbon::now(), function ($excel) use ($dataArray) {
            // Set the title
            $excel->setTitle('Macchinari Cliente');

            $excel->sheet('clienti', function ($sheet) use ($dataArray) {


                $sheet->cell('A1', function ($cell) {
                    // manipulate the cell
                    $cell->setValue('Cliente');

                });
                $sheet->cell('B1', function ($cell) {
                    // manipulate the cell
                    $cell->setValue('Marca');

                });
                $sheet->cell('C1', function ($cell) {
                    // manipulate the cell
                    $cell->setValue('Attrezzatura');

                });
                $sheet->cell('D1', function ($cell) {
                    // manipulate the cell
                    $cell->setValue('Verifica Periodica');

                });
                $sheet->cell('E1', function ($cell) {
                    // manipulate the cell
                    $cell->setValue('Ultima verifica');

                });


                
                $sheet->cells('A1:E1', function ($cells) {

                    $cells->setFontWeight('bold');
                    $cells->setBorder('solid', 'solid', 'solid', 'solid');

                });

                $sheet->fromArray($dataArray, null, 'A4', false, false);
            });


        })->store('xlsx', false, true);

        $file = storage_path('exports/' . $exp['file']);

        return response()->download($file)->deleteFileAfterSend(true);
    }

    public function MacchinariByClientId(Request $request) {
        $r = $request['params'];
        $id =$r['id'];

        $chars = preg_split('//', \Request::get('permissionAttribute'), -1, PREG_SPLIT_NO_EMPTY);
        $this->data['chars'] = $chars;
        
        if(isset($id)){
            $clienti = Clienti::where('id', $id)->first();
            $clienti->load('macchinari');
            
            $clienti->client_name = $clienti->azienda == 1 ? $clienti->ragione_sociale : $clienti->cognome . ' ' . $clienti->nome;

            if($clienti->macchinari->count() > 0){
                foreach ($clienti->macchinari as $key => $value) {
                    $value->client_name = $clienti->azienda == 1 ? $clienti->ragione_sociale : $clienti->cognome . ' ' . $clienti->nome;
                }
            }

            $this->data['clienti'] = $clienti;

            return response($this->data);
        }

        
    }

    public function getAllclients () {
        $clienti = Clienti::get();
            if($clienti->count() > 0){
                foreach ($clienti as $c) {
                    if ($c->azienda == 1) {
                        $data[] = [
                            'label' =>$c->ragione_sociale,
                            'value' => $c->ragione_sociale
                        ];
                    } else {
                        $data[] = [
                            'label' =>$c->cognome . ' ' . $c->nome,
                            'value' => $c->cognome . ' ' . $c->nome
                        ];
                    }
                }
            }else{
                $data[] = []; 
            }
            return response()->json(['clienti' => $data]);
    }

    public function MacchinariDelete (Request $request){
        
        $r = $request['params'];
        $id =$r['id'];

        if(isset($id)){

            $machinari = Macchinari::where('id_macchinario', '=', $id)->first();

            $macchinario_attivo = $machinari->macchinario_attivo == 1 ? 0 : 1;

            Macchinari::where('id_macchinario', '=', $id)->update(['macchinario_attivo' => $macchinario_attivo]);

            $machinari->load('interventis');

            if($macchinario_attivo == 1) {
                if(isset($machinari->interventis)){

                    foreach ($machinari->interventis as $key => $v) {
                        Interventi::where('id_intervento',$v->id_intervento)->update([
                            'da_programmare' => 1,
                            'Valido' => 1
                            ]);
                    }
                }
            }else{
                if(isset($machinari->interventis)){

                    foreach ($machinari->interventis as $key => $v) {
                        Interventi::where('id_intervento',$v->id_intervento)->update([
                            'da_programmare' => 1,
                            'Valido' => 0
                            ]);
                    }
                }
            }

            return response()->json(array('statut' => 'Success'), 200);
        } 
    }

    public function add($id = null, $see=null){

        $this->data['id'] = 'non';
        $this->data['see'] = 'non';
        $this->data['client'] = 'non';

        
        // ADD Macchinari /macchinari_add

        if($id == null && $see == null){
            return view('macchinari.macchinari_add2', $this->data);
        }

        // EDIT Macchinari /macchinari_add/1

        if($id!=null && $id!='cliente' && $see==null){
            
            $this->data['id'] = Macchinari::where('id_macchinario',$id)->first();
            $this->data['id']->load('cliente');

            $curent_date = Carbon::now()->format('Y-m-d');
            

            $interventi = $this->data['id']->interventis()->where([['da_programmare',1],["id_macchinario", $id]])
                          ->orWhere([['da_programmare',0], ['data','>=',$curent_date], ["id_macchinario", $id] ])->get();

            $interventi->load('tecnic');

            $this->data['id']->interventi = $interventi;

            if ($this->data['id']->cliente->azienda == 1) {
                $this->data['id']->cliente->name = $this->data['id']->cliente->ragione_sociale;
            } else {
                $this->data['id']->cliente->name = $this->data['id']->cliente->cognome.' '.$this->data['id']->cliente->nome;
            }
            
             

            return view('macchinari.macchinari_add2', $this->data);
        }

        // VIEW Macchinari /macchinari_add/1/view

        if(($id != null && $id!='cliente' && $see=='view')) {
            //$this->data['id'] = $id;
            $this->data['see'] = $see;
            $this->data['id'] = Macchinari::where('id_macchinario',$id)->first();

            $this->data['id']->load('cliente');
            
            $curent_date = Carbon::now()->format('Y-m-d');

            $interventi = $this->data['id']->interventis()->where('da_programmare',1)->orWhere([
                ['id_macchinario', $id],
                ['da_programmare',0],
                ['data','>=',$curent_date]
                ])->get();

            $interventi->load('tecnic');

            $this->data['id']->interventi = $interventi;

            if ($this->data['id']->cliente->azienda == 1) {
                $this->data['id']->cliente->name = $this->data['id']->cliente->ragione_sociale;
            } else {
                $this->data['id']->cliente->name = $this->data['id']->cliente->cognome.' '.$this->data['id']->cliente->nome;
            }

            return view('macchinari.macchinari_add2', $this->data);
        }

        // ADD Macchinari By client selected /macchinari_add/cliente/1

        if($id != null && $id=='cliente' && $see!='view' && $see!=null){
            $clienti = Clienti::where('id', $see)->first();
            $clienti->client_name = $clienti->azienda == 1 ? $clienti->ragione_sociale : $clienti->cognome . ' ' . $clienti->nome;
            $this->data['client'] = $clienti;

            return view('macchinari.macchinari_add2', $this->data);
        }

        
    }

    public function newMacchinariSave(Request $request){
        $r = $request['params']; 
        $tip = $r['tip']; 
        $intervent = $r['intervent'];
        $id_macchinari = $r['id_macchinari'];
        $r = $r['value'];


         /*$data = ['id_cliente'=>$r['id_cliente'], 'marca'=>$r['marca'], 'attrezzatura'=>$r['attrezzatura'], 
                  'matricola'=>$r['matricola'], 'anno_di_costrizione'=>$r['anno_di_costrizione'],
                  'numero_interno'=>$r['numero_interno'], 'verifica_periodica'=>$r['verifica_periodica'], 
                  'periodicita_verifica_mesi'=>$r['periodicita_verifica_mesi'], 
                  'data_ultima_verifica'=>$r['data_ultima_verifica'],                
                  'modello'=>$r['modello'], 'macchinario_attivo'=> 1
                ];*/

 
        if($tip == 'save'){
         $machinari  = Macchinari::create($r); 
         if($intervent != 'non')$machinari->interventis()->create($intervent);            
        }else if($tip == 'update'){
           Macchinari::where('id_macchinario', $id_macchinari)->update($r);
            if($intervent != 'non'){
                $machinari = Macchinari::where('id_macchinario',$id_macchinari)->first();
                $machinari->interventis()->where('da_programmare',1)->delete();
                
                $machinari->interventis()->create($intervent);
            }
        }


         

        

   
         Session::flash('success', 'L\'utente è stato aggiunto!');

        $marca = $machinari->marca !=null ? $machinari->marca.' | ' : '';
        $attrezzatura = $machinari->attrezzatura !=null ? $machinari->attrezzatura : '';
        $matricola = $machinari->matricola !=null ? ' | '.$machinari->matricola : '';
        $machinari->macchinari_name = $marca.' '.$attrezzatura.' '.$matricola;

        if(isset($machinari)){
            return response()->json(array('statut' => 'Success',
                                        'id_client' => $machinari->id_cliente,
                                        'machinario' => $machinari), 200);
        }
    }
}
