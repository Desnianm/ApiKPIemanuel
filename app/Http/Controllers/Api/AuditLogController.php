<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    // get semua audit log cuma owner
    public function index(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'aksi'    => 'nullable|string',
        ]);

        $query = AuditLog::with('user')->orderBy('created_at', 'desc');

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->aksi) {
            $query->where('aksi', $request->aksi);
        }

        $logs = $query->paginate(50);

        return response()->json([
            'data' => $logs
        ], 200);
    }

    // get audit log untuk user 
    public function byUser($userId)
    {
        $logs = AuditLog::with('user')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return response()->json([
            'data' => $logs
        ], 200);
    }
}