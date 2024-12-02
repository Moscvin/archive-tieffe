<?php

namespace App\Http\Controllers\Api\Client\Headquarter\Machinery;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Machinery;
use App\Http\Requests\Api\Machinery\NewMachineryRequest;

class NewController extends Controller
{
    public function main(NewMachineryRequest $request, $idClient, $idHeadquarter)
    {
        try {
            $data = [
                'id_sedi' => $idHeadquarter,
                'descrizione' => $request->descrizione,
                'modello' => $request->modello,
                'matricola' => $request->matricola,
                'anno' => $request->anno,
                'note' => $request->note,
                'attivo' => 1,
                'tetto' => $request->tetto? 1 : 0,
                '2tecnici' => $request['2tecnici']? 1 : 0,
                'tipologia' => $request->tipologia,
            ];
            $data['alldata'] = $this->alldata($data);
            $machinery = Machinery::create($data);
            return response()->json([
                'success' => 'ok',
                'id_macchinario' => $machinery->id_macchinario
            ]);
        } catch(\Throwable $e) {
            return response()->json([
                'success' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update(NewMachineryRequest $request, $idClient, $idHeadquarter){
      try {
        $data = [
          'id_sedi' => $idHeadquarter,
          'descrizione' => $request->descrizione,
          'modello' => $request->modello,
          'matricola' => $request->matricola,
          'anno' => $request->anno,
          'note' => $request->note,
          'attivo' => $request->attivo,
          'tetto' => $request->tetto? 1 : 0,
          '2tecnici' => $request['2tecnici']? 1 : 0,
        ];
        $data['alldata'] = $this->alldata($data);
        Machinery::where('id_macchinario', $request->id_macchinario)->update($data);
          return response()->json([
              'success' => 'ok'
          ]);
      } catch(\Throwable $e) {
          return response()->json([
              'success' => 'error',
              'message' => $e->getMessage()
          ]);
      }
    }

    function alldata($data){
      foreach ($data as $value) {
          if ($value == null || $value == '') {
              return false;
          }
      }
      return true;
    }
}
