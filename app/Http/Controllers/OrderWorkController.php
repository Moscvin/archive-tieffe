<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderWorkRequest;
use App\Models\OrdersWork;
use App\Repositories\OrderWorkRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;

class OrderWorkController extends Controller
{
    private $orderWorkRepository;
    private $userRepository;

    public function __construct(OrderWorkRepository $orderWorkRepository, UserRepository $userRepository)
    {
        $this->orderWorkRepository = $orderWorkRepository;
        $this->userRepository = $userRepository;
    }

    public function create()
    {
        $activeTechnicians = $this->userRepository->getActiveTechnicians();
        $order_id = \request('order_id');
        return view('order_works.create', compact('activeTechnicians', 'order_id'));
    }

    public function store(OrderWorkRequest $request)
    {
        $data = [
            'id_commessa' => $request->order_id,
            'tecnico' => $request->technician,
            'data' => Carbon::createFromFormat('d/m/Y', $request->date),
            'ore_lavorate' =>$request->hours,
            'descrizione' =>$request->description ?? null,
        ];
        OrdersWork::create($data);
        return redirect('/orders/'. $request->order_id . '/edit');
    }

    public function edit($id)
    {
        $chars = preg_split('//', \Request::get('permissionAttribute'), -1, PREG_SPLIT_NO_EMPTY);
        $page = $this->orderWorkRepository->getById($id);
        $page->activeTechnicians = $this->userRepository->getActiveTechnicians();
        return view('order_works.edit', compact('page', 'chars'));
    }

    public function update(OrderWorkRequest $request, $id)
    {
        $order_work = $this->orderWorkRepository->getById($id);
        $data = [
            'tecnico' => $request->technician,
            'data' => Carbon::createFromFormat('d/m/Y', $request->date),
            'ore_lavorate' =>$request->hours,
            'descrizione' =>$request->description ?? null,
        ];
        $order_work->update($data);
        return redirect('/orders/'. $order_work->id_commessa . '/edit');
    }

    public function delete($id)
    {
        try {
            $item = $this->orderWorkRepository->getById($id);
            if ($item) {
                if ($item->equipments) {
                    foreach ($item->equipments as $equipment) {
                        $equipment->delete();
                    }
                }
                $item->delete();
            }
            return response()->json(['status' => 'ok'], 204);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}