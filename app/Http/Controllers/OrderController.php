<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\OrdersWork;
use Illuminate\Http\Request;
use App\Repositories\OrderRepository;

class OrderController extends Controller
{
    private $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function index()
    {
        $chars = preg_split('//', \Request::get('permissionAttribute'), -1, PREG_SPLIT_NO_EMPTY);
        $items = $this->orderRepository->getAll();
        return view('orders.index', compact('items', 'chars'));
    }

    public function create()
    {
        return view('orders.create');
    }

    public function store(OrderRequest $request)
    {
        $data = [
            'nome' => $request->name,
            'note' => $request->note ?? null
        ];
        Order::create($data);
        return redirect('orders');
    }

    public function edit($id)
    {
        $chars = preg_split('//', \Request::get('permissionAttribute'), -1, PREG_SPLIT_NO_EMPTY);
        $page = $this->orderRepository->getById($id);
        return view('orders.edit', compact('page', 'chars'));
    }

    public function update(OrderRequest $request, $id)
    {
        $data = [
            'nome' => $request->name,
            'note' => $request->note ?? null
        ];
        Order::where('id_commessa', $id)->update($data);
        return redirect('orders');
    }

    public function show($id)
    {
        return view('management/macrocategory.edit');
    }

    public function delete($id)
    {
        try {
            $item = $this->orderRepository->getById($id);
            if ($item) {
                if ($item->equipments) {
                    foreach ($item->equipments as $equipment) {
                        $equipment->delete();
                    }
                }
                if ($item->orderWorks) {
                    foreach ($item->orderWorks as $orderWork) {
                        $orderWork->delete();
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

    public function lock(Request $request, $id)
    {
        try {
            $item = $this->orderRepository->getById($id);
            if($item) {
                $item->update([
                    'attiva' => !$request->status
                ]);
            }
            $item = $this->orderRepository->getById($id);
            return response()->json([], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
