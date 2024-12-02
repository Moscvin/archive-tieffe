<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\GeoDecoder;
use App\Models\Location;
use App\Models\Machinery;
use App\Http\Requests\LocationRequest;

class LocationController extends Controller
{
    public function add(Request $request, $id)
    {
        $this->data['chars'] = preg_split('//', \Request::get('permissionAttribute'), -1, PREG_SPLIT_NO_EMPTY);
        $this->data['types'] = $this->getTypes();
        $this->data['id_client'] = $id;
        $this->data['backRoute'] = $request->backRoute ?? '/customers';
        return view('location.location_add', $this->data);
    }

    public function edit(Request $request, $id)
    {
        $this->data['chars'] = preg_split('//', \Request::get('permissionAttribute'), -1, PREG_SPLIT_NO_EMPTY);
        $this->data['location'] = Location::where('id_sedi', $id)->with('client')->first();
        $this->data['types'] = $this->getTypes();
        $this->data['backRoute'] = $request->backRoute ?? '/customers';
        return view('location.location_edit', $this->data);
    }

    public function view(Request $request, $id)
    {
        $this->data['chars'] = preg_split('//', \Request::get('permissionAttribute'), -1, PREG_SPLIT_NO_EMPTY);
        $this->data['location'] = Location::where('id_sedi', $id)->with('client')->first();
        $this->data['backRoute'] = $request->backRoute ?? 'customers';
        return view('location.location_view', $this->data);
    }

    public function update(LocationRequest $request, $id)
    {
        $data = [
            'tipologia' => $request->tipologia ?? null,
            'indirizzo_via' => $request->indirizzo_sl ?? null,
            'indirizzo_civico' => $request->numero_civico_sl ?? null,
            'indirizzo_cap' => $request->cap_sl ?? null,
            'indirizzo_comune' => $request->comune_sl ?? null,
            'indirizzo_provincia' => $request->provincia_sl ?? null,
            'telefono1' => $request->telefono_1 ?? null,
            'telefono2' => $request->telefono_2 ?? null,
            'email' => $request->email ?? null,
            'note' => $request->note ?? null,
        ];

        $data['alldata'] = 1;
        $dataOld = $data;
        unset($data['telefono2'],$data['email'], $data['note']);
        foreach ($data as $value) {
            if ($value == null) {
                $dataOld['alldata'] = 0;
            }
        }

        $updatedLocation = Location::where('id_sedi', $id)->first();

        $coors = (new GeoDecoder($this->getAddress($updatedLocation->id_sedi)))->getCoors();
        $updatedLocation->operations()->update([
          'long' => ($coors->lng > 6 && $coors->lng < 20)? $coors->lng : null,
          'lat' => ($coors->lat > 36 && $coors->lat < 50)? $coors->lat : null
        ]);

        $updatedLocation->update($dataOld);

        return redirect()->to('/customer_add/'. $updatedLocation->id_cliente . "?backRoute=". ($request->backRoute ?? '/customers'));
    }

    public function save(LocationRequest $request, $id)
    {
        $data = [
            'id_cliente' => $id,
            'tipologia' => $request->tipologia ?? null,
            'indirizzo_via' => $request->indirizzo_sl ?? null,
            'indirizzo_civico' => $request->numero_civico_sl ?? null,
            'indirizzo_cap' => $request->cap_sl ?? null,
            'indirizzo_comune' => $request->comune_sl ?? null,
            'indirizzo_provincia' => $request->provincia_sl ?? null,
            'telefono1' => $request->telefono_1 ?? null,
            'telefono2' => $request->telefono_2 ?? null,
            'email' => $request->email ?? null,
            'note' => $request->note ?? null,
        ];
        $dataOld = $data;
        $dataOld['alldata'] = 1;
        unset($data['telefono2'],$data['email'], $data['note']);
        foreach ($data as $value) {
            if ($value == null) {
                $dataOld['alldata'] = 0;
            }
        }
        $newLocation = Location::create($dataOld);
        return redirect()->to('/customer_add/'. $id . "?backRoute=". ($request->backRoute ?? '/customers'));
    }

    public function delete($id)
    {
        $getLocation = Location::where('id_sedi', $id)->first();
        if($getLocation){
            $getLocation->delete();
        }
        return response()->json(array('statut' => 'Success'), 200);
    }

    public function getTypes()
    {
        $types = [
            'Sede legale',
            'Sede amministrativa',
            'Sede operativa',
            'Magazzino',
            'Altro'
        ];
        return $types;
    }

    private function getAddress($id)
    {
        return Location::where('id_sedi', $id)->first()->address ?? '';
    }
}
