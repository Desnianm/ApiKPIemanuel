<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Helpers\PeriodeHelper;
use App\Helpers\AuditHelper;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    //semua transaksi
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

    //detail 1 transaksi
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

    // transaksi dari unit bisnis dan periode
    public function byPeriode(Request $request, $unitBisnisId)
    {
        $request->validate([
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2000',
        ]);

        //  periode tanggal 26 bulan lalu - 25 bulan ini
        $range = PeriodeHelper::getRangePeriode($request->bulan, $request->tahun);

        $transaksi = Transaksi::with(['unitBisnis', 'user'])
            ->where('unit_bisnis_id', $unitBisnisId)
            ->whereBetween('tanggal_checkin', [$range['start'], $range['end']])
            ->orderBy('tanggal_checkin', 'desc')
            ->get();

        $totalNominal = $transaksi->sum('nominal');
        $totalOmset   = $transaksi->sum('omset');

        return response()->json([
            'data'            => $transaksi,
            'total_nominal'   => $totalNominal,
            'total_omset'     => $totalOmset,
            'total_transaksi' => $transaksi->count(),
            'periode'         => $range,
        ], 200);
    }

    // post untuk transaksi baru
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

        // hitung periode otomatis berdasarkan tanggal
        $periode = PeriodeHelper::hitungPeriode($tanggal);

        // cek duplikat
        if (Transaksi::isDuplikat($bookingKey)) {
            return response()->json([
                'message'     => 'Transaksi ini sudah pernah diinput sebelumnya (duplikat).',
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

        // catat audit log
        AuditHelper::log(
            userId: auth()->id(),
            aksi: 'create',
            tabelTarget: 'transaksi',
            recordId: $transaksi->id,
            dataBaru: $transaksi->toArray(),
        );

        return response()->json([
            'message'     => 'Transaksi berhasil diinput.',
            'data'        => $transaksi,
            'booking_key' => $bookingKey,
            'periode'     => $periode,
        ], 201);
    }

    // put update transaksi (owner only)
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

        // simpan data lama untuk audit log
        $dataLama = $transaksi->toArray();

        $transaksi->update($request->only([
            'nominal', 'omset', 'keterangan',
            'data_tambahan', 'bukti_bayar', 'status_transaksi'
        ]));

        // mencatat audit log
        AuditHelper::log(
            userId: auth()->id(),
            aksi: 'update',
            tabelTarget: 'transaksi',
            recordId: $transaksi->id,
            dataLama: $dataLama,
            dataBaru: $transaksi->toArray(),
        );

        return response()->json([
            'message' => 'Transaksi berhasil diupdate.',
            'data'    => $transaksi
        ], 200);
    }

    // delete transaksi (owner only)
    public function destroy($id)
    {
        $transaksi = Transaksi::find($id);

        if (!$transaksi) {
            return response()->json([
                'message' => 'Transaksi tidak ditemukan.'
            ], 404);
        }

        // mencatat audit log sebelum hapus
        AuditHelper::log(
            userId: auth()->id(),
            aksi: 'delete',
            tabelTarget: 'transaksi',
            recordId: $transaksi->id,
            dataLama: $transaksi->toArray(),
        );

        $transaksi->delete();

        return response()->json([
            'message' => 'Transaksi berhasil dihapus.'
        ], 200);
    }
}