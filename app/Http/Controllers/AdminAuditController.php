<?php

namespace App\Http\Controllers;

use App\Models\AdminAuditLog;
use Illuminate\Http\Request;

class AdminAuditController extends Controller
{
    public function index(Request $request)
    {
        $logs = AdminAuditLog::with(['actor', 'target'])
            ->when($request->filled('q'), fn ($q) => $q->where('action', 'like', '%'.$request->string('q').'%'))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('admin.audit.index', compact('logs'));
    }
}
