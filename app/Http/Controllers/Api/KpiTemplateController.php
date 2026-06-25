<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KpiTemplate;
use App\Models\KpiJenis;
use App\Models\FormTemplate;
use App\Models\FormField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KpiTemplateController extends Controller
{
    /**
     * GET /api/kpi-templates
     * Owner: lihat semua, karyawan/manajer: hanya unit bisnis sendiri
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = KpiTemplate::with([
            'unitBisnis',
            'kpiJenis',
            'formTemplate'
        ]);

        if ($user->role !== 'owner' && $user->role !== 'admin') {
            $query->where('unit_bisnis_id', $user->unit_bisnis_id);
        }

        $templates = $query->get();

        return response()->json([
            'success' => true,
            'data'    => $templates,
        ]);
    }

    /**
     * GET /api/kpi-templates/{id}
     * Detail 1 KPI template beserta relasi kpi_jenis & form_template
     */
    public function show($id)
    {
        $template = KpiTemplate::with([
            'unitBisnis',
            'kpiJenis',
            'formTemplate.formFields'
        ])->find($id);

        if (!$template) {
            return response()->json([
                'success' => false,
                'message' => 'KPI template tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $template,
        ]);
    }

    /**
     * GET /api/kpi-templates/unit-bisnis/{unitBisnisId}
     * Sekarang response include relasi kpi_jenis & form_template
     * supaya frontend tidak perlu fetch form-templates terpisah
     */
    public function byUnitBisnis(Request $request, $unitBisnisId)
    {
        $user = $request->user();

        // Karyawan/manajer hanya boleh akses unit bisnis sendiri
        if ($user->role === 'karyawan' || $user->role === 'manajer') {
            if ($user->unit_bisnis_id != $unitBisnisId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak bisa mengakses data unit bisnis lain',
                ], 403);
            }
        }

        $templates = KpiTemplate::with([
            'unitBisnis',
            'kpiJenis',                          // info katalog termasuk formula_type
            'formTemplate' => function ($query) {
                $query->withCount('formFields'); // hitung jumlah field tanpa load semua field
            }
        ])
        ->where('unit_bisnis_id', $unitBisnisId)
        ->get();

        return response()->json([
            'success' => true,
            'data'    => $templates,
        ]);
    }

    /**
     * POST /api/kpi-templates
     * 
     * BERUBAH TOTAL dari v1:
     * Sebelum: admin input nama, kategori, satuan manual + buat form template terpisah
     * Sekarang: cukup kirim {unit_bisnis_id, kpi_jenis_id}
     * Backend otomatis buat kpi_template + form_template + form_fields sekaligus
     * dalam 1 DB transaction
     */
    public function store(Request $request)
    {
        $request->validate([
            'unit_bisnis_id' => 'required|exists:unit_bisnis,id',
            'kpi_jenis_id'   => 'required|exists:kpi_jenis,id',
            // nama opsional: kalau tidak diisi, pakai nama dari katalog
            'nama'           => 'nullable|string|max:150',
        ]);

        // Ambil data katalog
        $kpiJenis = KpiJenis::find($request->kpi_jenis_id);

        // Pastikan jenis KPI masih aktif
        if (!$kpiJenis->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Jenis KPI ini sudah tidak aktif, pilih jenis KPI lain',
            ], 422);
        }

        // Cek apakah unit bisnis ini sudah punya KPI dengan jenis yang sama
        // mencegah duplikat KPI sejenis di unit bisnis yang sama
        $existing = KpiTemplate::where('unit_bisnis_id', $request->unit_bisnis_id)
            ->where('kpi_jenis_id', $request->kpi_jenis_id)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Unit bisnis ini sudah punya KPI jenis ' . $kpiJenis->nama,
            ], 422);
        }

        DB::beginTransaction();
        try {
            // 1. Buat KPI Template
            // nama diambil dari katalog kalau admin tidak override
            $template = KpiTemplate::create([
                'unit_bisnis_id' => $request->unit_bisnis_id,
                'kpi_jenis_id'   => $kpiJenis->id,
                'nama'           => $request->nama ?? $kpiJenis->nama,
                'kategori'       => $kpiJenis->kategori,
                'satuan'         => $kpiJenis->satuan_default,
                'deskripsi'      => $kpiJenis->deskripsi,
            ]);

            // 2. Buat Form Template otomatis
            $formTemplate = FormTemplate::create([
                'unit_bisnis_id'  => $request->unit_bisnis_id,
                'kpi_template_id' => $template->id,
                'nama'            => 'Form ' . $template->nama,
                'deskripsi'       => 'Form input otomatis untuk KPI ' . $template->nama,
                'is_active'       => true,
            ]);

            // 3. Buat Form Fields otomatis dari field_definitions di katalog
            // field_definitions sudah di-cast ke array di model KpiJenis
            $fieldDefinitions = is_array($kpiJenis->field_definitions) 
                ? $kpiJenis->field_definitions 
                : json_decode($kpiJenis->field_definitions, true);

            foreach ($fieldDefinitions as $index => $fieldDef) {
                FormField::create([
                    'form_template_id' => $formTemplate->id,
                    'kpi_template_id'  => $template->id, // link ke KPI
                    'is_kpi_field'     => true,           // tandai sebagai field dari katalog
                    'label'            => $fieldDef['label'],
                    'tipe'             => $fieldDef['tipe'],
                    'wajib'            => $fieldDef['wajib'] ?? false,
                    'urutan'           => $index + 1,
                    'options'          => null,
                ]);
            }

            DB::commit();

            // Load semua relasi untuk response lengkap
            $template->load([
                'unitBisnis',
                'kpiJenis',
                'formTemplate.formFields'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'KPI template berhasil dibuat beserta form-nya',
                'data'    => $template,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat KPI template: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * PUT /api/kpi-templates/{id}
     * Hanya bisa update nama & deskripsi
     * kategori & satuan tidak bisa diubah karena sudah terikat katalog
     */
    public function update(Request $request, $id)
    {
        $template = KpiTemplate::find($id);

        if (!$template) {
            return response()->json([
                'success' => false,
                'message' => 'KPI template tidak ditemukan',
            ], 404);
        }

        $request->validate([
            'nama'      => 'sometimes|string|max:150',
            'deskripsi' => 'nullable|string',
        ]);

        $template->update($request->only(['nama', 'deskripsi']));

        $template->load(['unitBisnis', 'kpiJenis', 'formTemplate']);

        return response()->json([
            'success' => true,
            'message' => 'KPI template berhasil diupdate',
            'data'    => $template,
        ]);
    }

    /**
     * DELETE /api/kpi-templates/{id}
     * 
     * BERUBAH dari v1:
     * Sekarang cascade delete dalam 1 DB transaction:
     * hapus kpi_template → form_template & kpi_periods ikut terhapus otomatis
     * Frontend tidak perlu hapus satu-satu lagi
     */
    public function destroy($id)
    {
        $template = KpiTemplate::with([
            'formTemplate',
            'kpiPeriods'
        ])->find($id);

        if (!$template) {
            return response()->json([
                'success' => false,
                'message' => 'KPI template tidak ditemukan',
            ], 404);
        }

        DB::beginTransaction();
        try {
            // Hapus form_template terkait (form_fields ikut terhapus via cascade DB)
            if ($template->formTemplate) {
                $template->formTemplate->delete();
            }

            // Hapus semua kpi_periods terkait
            $template->kpiPeriods()->delete();

            // Hapus kpi_template nya (soft delete karena model pakai SoftDeletes)
            $template->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'KPI template beserta form dan periode terkait berhasil dihapus',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus KPI template: ' . $e->getMessage(),
            ], 500);
        }
    }
}