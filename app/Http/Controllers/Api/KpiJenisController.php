<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KpiJenis;
use Illuminate\Http\Request;

class KpiJenisController extends Controller
{
    /**
     * GET /api/kpi-jenis
     * Daftar semua jenis KPI dari katalog (untuk dropdown admin)
     * Hanya tampilkan yang is_active = true
     */
    public function index()
    {
        $data = KpiJenis::where('is_active', true)
            ->orderBy('kategori')
            ->orderBy('nama')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }

    /**
     * GET /api/kpi-jenis/{id}
     * Detail 1 jenis KPI dari katalog
     * Termasuk field_definitions supaya frontend tahu field apa yang akan auto-dibuat
     */
    public function show($id)
    {
        $kpiJenis = KpiJenis::find($id);

        if (!$kpiJenis) {
            return response()->json([
                'success' => false,
                'message' => 'Jenis KPI tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $kpiJenis,
        ]);
    }

    /**
     * POST /api/kpi-jenis
     * Owner tambah jenis KPI baru ke katalog (opsional)
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode'              => 'required|string|max:50|unique:kpi_jenis,kode',
            'nama'              => 'required|string|max:150',
            'kategori'          => 'required|in:sales,operasional,keuangan,sdm,lainnya',
            'satuan_default'    => 'required|in:rupiah,persen,unit,malam,lainnya',
            'formula_type'      => 'required|in:sum,average,count,last_value',
            'field_definitions' => 'required|array|min:1',
            'field_definitions.*.label' => 'required|string',
            'field_definitions.*.tipe'  => 'required|in:text,number,date,select,textarea',
            'field_definitions.*.wajib' => 'boolean',
            'deskripsi'         => 'nullable|string',
        ]);

        $kpiJenis = KpiJenis::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Jenis KPI berhasil ditambahkan ke katalog',
            'data'    => $kpiJenis,
        ], 201);
    }

    /**
     * PUT /api/kpi-jenis/{id}
     * Owner update jenis KPI di katalog (opsional)
     */
    public function update(Request $request, $id)
    {
        $kpiJenis = KpiJenis::find($id);

        if (!$kpiJenis) {
            return response()->json([
                'success' => false,
                'message' => 'Jenis KPI tidak ditemukan',
            ], 404);
        }

        $request->validate([
            'kode'              => 'sometimes|string|max:50|unique:kpi_jenis,kode,' . $id,
            'nama'              => 'sometimes|string|max:150',
            'kategori'          => 'sometimes|in:sales,operasional,keuangan,sdm,lainnya',
            'satuan_default'    => 'sometimes|in:rupiah,persen,unit,malam,lainnya',
            'formula_type'      => 'sometimes|in:sum,average,count,last_value',
            'field_definitions' => 'sometimes|array|min:1',
            'field_definitions.*.label' => 'required_with:field_definitions|string',
            'field_definitions.*.tipe'  => 'required_with:field_definitions|in:text,number,date,select,textarea',
            'field_definitions.*.wajib' => 'boolean',
            'deskripsi'         => 'nullable|string',
        ]);

        $kpiJenis->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Jenis KPI berhasil diupdate',
            'data'    => $kpiJenis,
        ]);
    }

    /**
     * PATCH /api/kpi-jenis/{id}/toggle
     * Owner aktifkan/nonaktifkan jenis KPI
     * Kalau dinonaktifkan, tidak muncul di dropdown tapi KPI yang sudah dibuat tetap jalan
     */
    public function toggle($id)
    {
        $kpiJenis = KpiJenis::find($id);

        if (!$kpiJenis) {
            return response()->json([
                'success' => false,
                'message' => 'Jenis KPI tidak ditemukan',
            ], 404);
        }

        $kpiJenis->update([
            'is_active' => !$kpiJenis->is_active,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status jenis KPI berhasil diubah',
            'data'    => [
                'id'        => $kpiJenis->id,
                'nama'      => $kpiJenis->nama,
                'is_active' => $kpiJenis->is_active,
            ],
        ]);
    }
}