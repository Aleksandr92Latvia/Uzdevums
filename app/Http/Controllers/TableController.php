<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Table;
use App\Models\Reports;

class TableController extends Controller
{
    public function show_table(Request $request)
    {
        $report_list = new Reports();
        return response()->json($report_list->all());
    }
}
