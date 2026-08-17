<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BobotKategoriPeriode;
use App\Models\KategoriUnitBisnis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BobotKategoriController extends Controller
{
    //list untuk semua bobot kategori periode tertentu
    public function index(Request $request)
    {
        $request->validate([
            'periode' => 'required|integer|min:2000',
        ]);

        $periode = (int) $request->periode;

        $kategoriList = KategoriUnitBisnis::where('is_active', true)
            ->orderBy('urutan')
            ->get();

        $bobotExisting = BobotKategoriPeriode::where('periode', $periode)
            ->get()
            ->keyBy('kategori_id');

        $data = $kategoriList->map(function (KategoriUnitBisnis $kategori) use ($bobotExisting, $periode) {
            $bobot = $bobotExisting->get($kategori->id);

            return [
                'kategori_id'        => $kategori->id,
                'nama_kategori'      => $kategori->nama,
                'periode'            => $periode,
                'bobot_persen'       => $bobot ? (float) $bobot->bobot_persen : 0,
                'ditetapkan_oleh'    => $bobot?->ditetapkan_oleh,
                'tanggal_ditetapkan' => $bobot?->tanggal_ditetapkan,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $data->values(),
        ]);
    }

    // simpan dan up bobot kategori buat 1 periode, total bobot persen semua kategori dalam array harus = 100
    public function store(Request $request)
    {
        $validated = $request->validate([
            'periode'                       => 'required|integer|min:2000',
            'bobot'                          => 'required|array|min:1',
            'bobot.*.kategori_id'            => 'required|exists:kategori_unit_bisnis,id',
            'bobot.*.bobot_persen'           => 'required|numeric|min:0|max:100',
        ]);

        $total = collect($validated['bobot'])->sum('bobot_persen');

        if (abs($total - 100) > 0.01) {
            return response()->json([
                'success' => false,
                'message' => "Total bobot semua kategori harus 100%. Total saat ini: {$total}%",
            ], 422);
        }

        DB::beginTransaction();
        try {
            $user = $request->user();

            foreach ($validated['bobot'] as $item) {
                BobotKategoriPeriode::updateOrCreate(
                    [
                        'kategori_id' => $item['kategori_id'],
                        'periode'     => $validated['periode'],
                    ],
                    [
                        'bobot_persen'       => $item['bobot_persen'],
                        'ditetapkan_oleh'    => $user->id,
                        'tanggal_ditetapkan' => now(),
                    ]
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Bobot kategori berhasil disimpan',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan bobot kategori: ' . $e->getMessage(),
            ], 500);
        }
    }
}