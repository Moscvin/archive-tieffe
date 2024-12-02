<?php

namespace App\Http\Controllers\Operation;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Operation\Operation;

class OperationController extends Controller
{
    public function destroy($id)
    {
        $operation = Operation::where('id_intervento', $id)->first();
        $operation->delete();
        return response()->json([], 204);
    }
}
