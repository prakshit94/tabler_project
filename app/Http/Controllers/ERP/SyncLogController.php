<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\SyncLog;
use Illuminate\Http\Request;

class SyncLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = SyncLog::latest()->paginate(20);
        return view('erp.sync-logs.index', compact('logs'));
    }
}
