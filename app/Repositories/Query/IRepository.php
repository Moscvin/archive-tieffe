<?php

namespace App\Repositories\Query;

use Illuminate\Http\Request;

interface IRepository
{
    public function getDataTableFilteredAndFormatted(Request $request, array $chars);
    public function getDataTablesFiltered(Request $request);
    public function getItemsFormatted(array $dataTablesItems, array $chars);
    
}