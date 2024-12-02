<?php

namespace App\Http\Controllers;

use App\Models\EquipmentOrderIntervention;
use App\Repositories\MaterialRepository;
use App\Repositories\OrderWorkRepository;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    private $materialRepository;
    private $orderWorkRepository;

    public function __construct(MaterialRepository $materialRepository, OrderWorkRepository $orderWorkRepository)
    {
        $this->materialRepository = $materialRepository;
        $this->orderWorkRepository = $orderWorkRepository;
    }

    public function edit($id)
    {
        $chars = preg_split('//', \Request::get('permissionAttribute'), -1, PREG_SPLIT_NO_EMPTY);
        $materials = $this->materialRepository->getByWorkId($id);
        $orderWork = $this->orderWorkRepository->getById($id);
        return view('materials.edit', compact('materials', 'orderWork', 'chars'));
    }

    public function delete($id)
    {
        try {
            $item = $this->materialRepository->getById($id);
            if ($item) {
                $item->delete();
            }
            return response()->json(['status' => 'ok'], 204);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function materialAdd(Request $request)
    {
        try {
            $data = [
                'codice' => $request->code ?? null,
                'descrizione' => $request->description,
                'quantita' => $request->quantity,
                'id_lavoro' => $request->work_id,
            ];
            $new_material = EquipmentOrderIntervention::create($data);
            return response()->json([
                'material_id'   => $new_material->id_materiale,
            ], 200);
        } catch(\Throwable $e) {
            return response()->json([
                'message' => 'Error'
            ], 400);
        }
    }

    public function materialUpdate(Request $request, $id)
    {
        try {
            $material = $this->materialRepository->getById($id);
            $data = [
                'codice' => $request->code ?? null,
                'descrizione' => $request->description,
                'quantita' => $request->quantity,
                'id_lavoro' => $request->work_id,
            ];
            $material->update($data);
            return response()->json([
                'material_id'   => $material->id_materiale,
            ], 200);
        } catch(\Throwable $e) {
            return response()->json([
                'message' => 'Error'
            ], 400);
        }
    }
}