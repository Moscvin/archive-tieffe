<?php

namespace App\Http\Controllers\Report;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\ReportRepository;

class ReportController extends Controller
{
    private $reportRepository;

    public function __construct(ReportRepository $reportRepository)
    {
        $this->reportRepository = $reportRepository;
    }

    public function delete($id)
    {
        $item = $this->reportRepository->getById($id);
        if($item) {
            $item->delete();
        }
        return response()->json([], 204);
    }
}
