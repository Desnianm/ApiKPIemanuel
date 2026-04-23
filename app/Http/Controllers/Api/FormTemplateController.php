<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FormTemplate;
use App\Models\FormField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FormTemplateController extends Controller
{
    /**
     * GET /api/form-templates
     * Owner: lihat semua form template
     * Karyawan: hanya lihat form template unit bisnis sendiri
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = FormTemplate::with(['unitBisnis', 'formFields.kpiTemplate']);

        // Karyawan hanya lihat form template unit bisnis sendiri
        if ($user->role === 'karyawan' || $user->role === 'manajer') {
            $query->where('unit_bisnis_id', $user->unit_bisnis_id);
        }

        $data = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }

    /**
     * GET /api/form-templates/{id}
     * Detail satu form template beserta semua fields-nya
     */
    public function show($id)
    {
        $formTemplate = FormTemplate::with(['unitBisnis', 'formFields.kpiTemplate'])
            ->find($id);

        if (!$formTemplate) {
            return response()->json([
                'success' => false,
                'message' => 'Form template tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $formTemplate,
        ]);
    }

    /**
     * GET /api/form-templates/unit-bisnis/{unitBisnisId}
     * Lihat semua form template berdasarkan unit bisnis
     */
    public function byUnitBisnis($unitBisnisId)
    {
        $data = FormTemplate::with(['formFields.kpiTemplate'])
            ->where('unit_bisnis_id', $unitBisnisId)
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }

    /**
     * POST /api/form-templates
     * Owner membuat form template baru beserta fields-nya sekaligus
     * 
     * Body contoh:
     * {
     *   "unit_bisnis_id": 1,
     *   "nama": "Form Pendapatan Harian",
     *   "deskripsi": "Form untuk input pendapatan harian villa",
     *   "is_active": true,
     *   "fields": [
     *     {
     *       "label": "Jumlah Tamu",
     *       "tipe": "number",
     *       "wajib": true,
     *       "urutan": 1,
     *       "kpi_template_id": null
     *     },
     *     {
     *       "label": "Total Pendapatan",
     *       "tipe": "number",
     *       "wajib": true,
     *       "urutan": 2,
     *       "kpi_template_id": 3
     *     },
     *     {
     *       "label": "Catatan",
     *       "tipe": "textarea",
     *       "wajib": false,
     *       "urutan": 3,
     *       "kpi_template_id": null
     *     }
     *   ]
     * }
     */
    public function store(Request $request)
    {
        $request->validate([
            'unit_bisnis_id'          => 'required|exists:unit_bisnis,id',
            'nama'                    => 'required|string|max:150',
            'deskripsi'               => 'nullable|string',
            'is_active'               => 'boolean',
            'fields'                  => 'required|array|min:1',
            'fields.*.label'          => 'required|string|max:150',
            'fields.*.tipe'           => 'required|in:text,number,date,select,textarea',
            'fields.*.wajib'          => 'boolean',
            'fields.*.urutan'         => 'integer',
            'fields.*.options'        => 'nullable|array',
            'fields.*.kpi_template_id' => 'nullable|exists:kpi_templates,id',
        ]);

        // Pakai DB transaction supaya kalau ada error, semua rollback
        DB::beginTransaction();
        try {
            // 1. Buat form template
            $formTemplate = FormTemplate::create([
                'unit_bisnis_id' => $request->unit_bisnis_id,
                'nama'           => $request->nama,
                'deskripsi'      => $request->deskripsi,
                'is_active'      => $request->is_active ?? true,
            ]);

            // 2. Buat semua fields-nya sekaligus
            foreach ($request->fields as $field) {
                FormField::create([
                    'form_template_id' => $formTemplate->id,
                    'kpi_template_id'  => $field['kpi_template_id'] ?? null,
                    'label'            => $field['label'],
                    'tipe'             => $field['tipe'],
                    'options'          => $field['options'] ?? null,
                    'wajib'            => $field['wajib'] ?? false,
                    'urutan'           => $field['urutan'] ?? 0,
                ]);
            }

            DB::commit();

            // Load relasi untuk response
            $formTemplate->load(['unitBisnis', 'formFields.kpiTemplate']);

            return response()->json([
                'success' => true,
                'message' => 'Form template berhasil dibuat',
                'data'    => $formTemplate,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat form template: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * PUT /api/form-templates/{id}
     * Owner update form template.
     * Kalau kirim "fields", maka fields lama dihapus dan diganti yang baru.
     * Kalau tidak kirim "fields", hanya update info dasar form template saja.
     */
    public function update(Request $request, $id)
    {
        $formTemplate = FormTemplate::find($id);

        if (!$formTemplate) {
            return response()->json([
                'success' => false,
                'message' => 'Form template tidak ditemukan',
            ], 404);
        }

        $request->validate([
            'unit_bisnis_id'           => 'sometimes|exists:unit_bisnis,id',
            'nama'                     => 'sometimes|string|max:150',
            'deskripsi'                => 'nullable|string',
            'is_active'                => 'boolean',
            'fields'                   => 'sometimes|array|min:1',
            'fields.*.label'           => 'required_with:fields|string|max:150',
            'fields.*.tipe'            => 'required_with:fields|in:text,number,date,select,textarea',
            'fields.*.wajib'           => 'boolean',
            'fields.*.urutan'          => 'integer',
            'fields.*.options'         => 'nullable|array',
            'fields.*.kpi_template_id' => 'nullable|exists:kpi_templates,id',
        ]);

        DB::beginTransaction();
        try {
            // Update info dasar
            $formTemplate->update($request->only([
                'unit_bisnis_id', 'nama', 'deskripsi', 'is_active'
            ]));

            // Kalau request kirim fields → hapus fields lama, buat yang baru
            if ($request->has('fields')) {
                // Hapus semua fields lama
                FormField::where('form_template_id', $formTemplate->id)->delete();

                // Buat fields baru
                foreach ($request->fields as $field) {
                    FormField::create([
                        'form_template_id' => $formTemplate->id,
                        'kpi_template_id'  => $field['kpi_template_id'] ?? null,
                        'label'            => $field['label'],
                        'tipe'             => $field['tipe'],
                        'options'          => $field['options'] ?? null,
                        'wajib'            => $field['wajib'] ?? false,
                        'urutan'           => $field['urutan'] ?? 0,
                    ]);
                }
            }

            DB::commit();

            $formTemplate->load(['unitBisnis', 'formFields.kpiTemplate']);

            return response()->json([
                'success' => true,
                'message' => 'Form template berhasil diupdate',
                'data'    => $formTemplate,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal update form template: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * DELETE /api/form-templates/{id}
     * Owner hapus form template (fields otomatis terhapus karena cascade)
     */
    public function destroy($id)
    {
        $formTemplate = FormTemplate::find($id);

        if (!$formTemplate) {
            return response()->json([
                'success' => false,
                'message' => 'Form template tidak ditemukan',
            ], 404);
        }

        $formTemplate->delete();

        return response()->json([
            'success' => true,
            'message' => 'Form template berhasil dihapus',
        ]);
    }

    /**
     * PATCH /api/form-templates/{id}/toggle
     * Owner aktifkan/nonaktifkan form template
     */
    public function toggle($id)
    {
        $formTemplate = FormTemplate::find($id);

        if (!$formTemplate) {
            return response()->json([
                'success' => false,
                'message' => 'Form template tidak ditemukan',
            ], 404);
        }

        $formTemplate->update([
            'is_active' => !$formTemplate->is_active,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status form template berhasil diubah',
            'data'    => [
                'id'        => $formTemplate->id,
                'is_active' => $formTemplate->is_active,
            ],
        ]);
    }
}