<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PayrollReminder;
use App\Models\User;
use Illuminate\Http\Request;

class PayrollReminderController extends Controller
{
    // GET semua payroll reminder (owner only)
    public function index(Request $request)
    {
        $request->validate([
            'bulan' => 'nullable|integer|min:1|max:12',
            'tahun' => 'nullable|integer|min:2000',
        ]);

        $query = PayrollReminder::with('user');

        if ($request->bulan) {
            $query->where('periode_bulan', $request->bulan);
        }
        if ($request->tahun) {
            $query->where('periode_tahun', $request->tahun);
        }

        $payroll = $query->orderBy('periode_tahun', 'desc')
            ->orderBy('periode_bulan', 'desc')
            ->get();

        return response()->json([
            'data'        => $payroll,
            'total_gaji'  => $payroll->sum('total_gaji'),
        ], 200);
    }

    // GET payroll reminder by user
    public function byUser($userId)
    {
        $payroll = PayrollReminder::with('user')
            ->where('user_id', $userId)
            ->orderBy('periode_tahun', 'desc')
            ->orderBy('periode_bulan', 'desc')
            ->get();

        return response()->json([
            'data' => $payroll
        ], 200);
    }

    // POST buat payroll reminder baru (owner only)
    public function store(Request $request)
    {
        $request->validate([
            'user_id'       => 'required|exists:users,id',
            'periode_bulan' => 'required|integer|min:1|max:12',
            'periode_tahun' => 'required|integer|min:2000',
            'gaji_pokok'    => 'required|numeric|min:0',
            'tunjangan'     => 'nullable|numeric|min:0',
            'potongan'      => 'nullable|numeric|min:0',
            'catatan'       => 'nullable|string',
        ]);

        // Cek duplikat periode per user
        $existing = PayrollReminder::where('user_id', $request->user_id)
            ->where('periode_bulan', $request->periode_bulan)
            ->where('periode_tahun', $request->periode_tahun)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Payroll untuk periode ini sudah ada.',
                'data'    => $existing
            ], 422);
        }

        $tunjangan  = $request->tunjangan ?? 0;
        $potongan   = $request->potongan ?? 0;
        $totalGaji  = $request->gaji_pokok + $tunjangan - $potongan;

        $payroll = PayrollReminder::create([
            'user_id'          => $request->user_id,
            'periode_bulan'    => $request->periode_bulan,
            'periode_tahun'    => $request->periode_tahun,
            'gaji_pokok'       => $request->gaji_pokok,
            'tunjangan'        => $tunjangan,
            'potongan'         => $potongan,
            'total_gaji'       => $totalGaji,
            'sudah_diingatkan' => false,
            'catatan'          => $request->catatan,
        ]);

        return response()->json([
            'message' => 'Payroll reminder berhasil dibuat.',
            'data'    => $payroll
        ], 201);
    }

    // PUT update payroll reminder (owner only)
    public function update(Request $request, $id)
    {
        $payroll = PayrollReminder::find($id);

        if (!$payroll) {
            return response()->json([
                'message' => 'Payroll reminder tidak ditemukan.'
            ], 404);
        }

        $request->validate([
            'gaji_pokok' => 'sometimes|numeric|min:0',
            'tunjangan'  => 'nullable|numeric|min:0',
            'potongan'   => 'nullable|numeric|min:0',
            'catatan'    => 'nullable|string',
        ]);

        $gajiPokok = $request->gaji_pokok ?? $payroll->gaji_pokok;
        $tunjangan = $request->tunjangan ?? $payroll->tunjangan;
        $potongan  = $request->potongan ?? $payroll->potongan;
        $totalGaji = $gajiPokok + $tunjangan - $potongan;

        $payroll->update([
            'gaji_pokok'  => $gajiPokok,
            'tunjangan'   => $tunjangan,
            'potongan'    => $potongan,
            'total_gaji'  => $totalGaji,
            'catatan'     => $request->catatan ?? $payroll->catatan,
        ]);

        return response()->json([
            'message' => 'Payroll reminder berhasil diupdate.',
            'data'    => $payroll
        ], 200);
    }

    // PATCH toggle sudah diingatkan (owner only)
    public function toggleReminder($id)
    {
        $payroll = PayrollReminder::find($id);

        if (!$payroll) {
            return response()->json([
                'message' => 'Payroll reminder tidak ditemukan.'
            ], 404);
        }

        $payroll->update([
            'sudah_diingatkan' => !$payroll->sudah_diingatkan,
            'tanggal_reminder' => !$payroll->sudah_diingatkan ? now()->format('Y-m-d') : null,
        ]);

        $status = $payroll->sudah_diingatkan ? 'sudah diingatkan' : 'belum diingatkan';

        return response()->json([
            'message' => "Payroll reminder berhasil ditandai {$status}.",
            'data'    => $payroll
        ], 200);
    }

    // DELETE payroll reminder (owner only)
    public function destroy($id)
    {
        $payroll = PayrollReminder::find($id);

        if (!$payroll) {
            return response()->json([
                'message' => 'Payroll reminder tidak ditemukan.'
            ], 404);
        }

        $payroll->delete();

        return response()->json([
            'message' => 'Payroll reminder berhasil dihapus.'
        ], 200);
    }
}