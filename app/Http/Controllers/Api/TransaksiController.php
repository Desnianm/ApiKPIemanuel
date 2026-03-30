<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    // GET semua transaksi
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'owner') {
            $transaksi = Transaksi::with(['unitBisnis', 'user'])
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $transaksi = Transaksi::with(['unitBisnis', 'user'])
                ->where('unit_bisnis_id', $user->unit_bisnis_id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return response()->json([
            'data' => $transaksi
        ], 200);
    }

    // GET detail 1 transaksi
    public function show($id)
    {
        $transaksi = Transaksi::with(['unitBisnis', 'user'])->find($id);

        if (!$transaksi) {
            return response()->json([
                'message' => 'Transaksi tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'data' => $transaksi
        ], 200);
    }

    // GET transaksi by unit bisnis & periode
    public function byPeriode(Request $request, $unitBisnisId)
    {
        $request->validate([
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2000',
        ]);

        $transaksi = Transaksi::with(['unitBisnis', 'user'])
            ->where('unit_bisnis_id', $unitBisnisId)
            ->whereMonth('created_at', $request->bulan)
            ->whereYear('created_at', $request->tahun)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalNominal = $transaksi->sum('nominal');
        $totalOmset   = $transaksi->sum('omset');

        return response()->json([
            'data'          => $transaksi,
            'total_nominal' => $totalNominal,
            'total_omset'   => $totalOmset,
            'total_transaksi' => $transaksi->count(),
        ], 200);
    }

    // POST buat transaksi baru
    public function store(Request $request)
    {
        $request->validate([
            'unit_bisnis_id'   => 'required|exists:unit_bisnis,id',
            'nama_customer'    => 'required|string|max:100',
            'no_hp'            => 'required|string|max:20',
            'tipe_transaksi'   => 'required|in:booking_fee,dp,pelunasan,sewa_harian,penjualan,lainnya',
            'nominal'          => 'required|numeric|min:0',
            'omset'            => 'nullable|numeric|min:0',
            'tanggal_checkin'  => 'nullable|date',
            'tanggal_checkout' => 'nullable|date',
            'keterangan'       => 'nullable|string',
            'data_tambahan'    => 'nullable|array',
            'bukti_bayar'      => 'nullable|string',
            'booking_id'       => 'nullable|string|max:100',
            'status_transaksi' => 'nullable|string|max:50',
        ]);

        // Generate booking key
        $tanggal    = $request->tanggal_checkin ?? now()->format('Y-m-d');
        $bookingKey = Transaksi::generateBookingKey(
            $request->nama_customer,
            $request->no_hp,
            $tanggal
        );

        // Cek duplikat
        if (Transaksi::isDuplikat($bookingKey)) {
            return response()->json([
                'message' => 'Transaksi ini sudah pernah diinput sebelumnya (duplikat).',
                'booking_key' => $bookingKey,
            ], 422);
        }

        $transaksi = Transaksi::create([
            'unit_bisnis_id'   => $request->unit_bisnis_id,
            'user_id'          => $request->user()->id,
            'booking_key'      => $bookingKey,
            'nama_customer'    => $request->nama_customer,
            'no_hp'            => $request->no_hp,
            'tipe_transaksi'   => $request->tipe_transaksi,
            'nominal'          => $request->nominal,
            'omset'            => $request->omset,
            'tanggal_checkin'  => $request->tanggal_checkin,
            'tanggal_checkout' => $request->tanggal_checkout,
            'keterangan'       => $request->keterangan,
            'data_tambahan'    => $request->data_tambahan,
            'bukti_bayar'      => $request->bukti_bayar,
            'booking_id'       => $request->booking_id,
            'status_transaksi' => $request->status_transaksi,
        ]);

        return response()->json([
            'message'     => 'Transaksi berhasil diinput.',
            'data'        => $transaksi,
            'booking_key' => $bookingKey,
        ], 201);
    }

    // PUT update transaksi (owner only)
    public function update(Request $request, $id)
    {
        $transaksi = Transaksi::find($id);

        if (!$transaksi) {
            return response()->json([
                'message' => 'Transaksi tidak ditemukan.'
            ], 404);
        }

        $request->validate([
            'nominal'          => 'sometimes|numeric|min:0',
            'omset'            => 'nullable|numeric|min:0',
            'keterangan'       => 'nullable|string',
            'data_tambahan'    => 'nullable|array',
            'bukti_bayar'      => 'nullable|string',
            'status_transaksi' => 'nullable|string|max:50',
        ]);

        $transaksi->update($request->only([
            'nominal', 'omset', 'keterangan',
            'data_tambahan', 'bukti_bayar', 'status_transaksi'
        ]));

        return response()->json([
            'message' => 'Transaksi berhasil diupdate.',
            'data'    => $transaksi
        ], 200);
    }

    // DELETE transaksi (owner only)
    public function destroy($id)
    {
        $transaksi = Transaksi::find($id);

        if (!$transaksi) {
            return response()->json([
                'message' => 'Transaksi tidak ditemukan.'
            ], 404);
        }

        $transaksi->delete();

        return response()->json([
            'message' => 'Transaksi berhasil dihapus.'
        ], 200);
    }
}