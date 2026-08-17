<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FormTemplate;
use App\Models\FormField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FormTemplateController extends Controller
{

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

    public function byUnitBisnis(Request $request, $unitBisnisId)
    {
        $user = $request->user();

        // karyawan/manajer hanya boleh akses unit bisnis sendiri
        if ($user->role === 'karyawan' || $user->role === 'manajer') {
            if ($user->unit_bisnis_id != $unitBisnisId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak bisa mengakses data unit bisnis lain',
                ], 403);
            }
        }

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
            // membuat form template
            $formTemplate = FormTemplate::create([
                'unit_bisnis_id' => $request->unit_bisnis_id,
                'nama'           => $request->nama,
                'deskripsi'      => $request->deskripsi,
                'is_active'      => $request->is_active ?? true,
            ]);

            // buat semua fields-nya sekaligus
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

            // load relasi untuk response
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
            // update info dasar
            $formTemplate->update($request->only([
                'unit_bisnis_id', 'nama', 'deskripsi', 'is_active'
            ]));

            // Kalau request kirim fields akan hapus fields lama, buat yang baru
            if ($request->has('fields')) {
                // Hapus semua fields lama
                FormField::where('form_template_id', $formTemplate->id)->delete();

                // buat fields baru
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
 
    public function addField(Request $request, $id)
    {
        $formTemplate = FormTemplate::find($id);

        if (!$formTemplate) {
            return response()->json([
                'success' => false,
                'message' => 'Form template tidak ditemukan',
            ], 404);
        }

        $request->validate([
            'label'   => 'required|string|max:150',
            'tipe'    => 'required|in:text,number,date,select,textarea',
            'wajib'   => 'boolean',
            'urutan'  => 'nullable|integer',
            'options' => 'nullable|array', // untuk tipe select
        ]);

        // hitung urutan otomatis kalau tidak diisi
        // field baru akan diletakkan setelah field yang sudah ada
        $urutanTerakhir = FormField::where('form_template_id', $formTemplate->id)
            ->max('urutan') ?? 0;

        $field = FormField::create([
            'form_template_id' => $formTemplate->id,
            'kpi_template_id'  => null,
            'is_kpi_field'     => false,
            'label'            => $request->label,
            'tipe'             => $request->tipe,
            'wajib'            => $request->wajib ?? false,
            'urutan'           => $request->urutan ?? ($urutanTerakhir + 1),
            'options'          => $request->options,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Field berhasil ditambahkan ke form',
            'data'    => $field,
        ], 201);
    }


    public function deleteField($id, $fieldId)
    {
        $formTemplate = FormTemplate::find($id);

        if (!$formTemplate) {
            return response()->json([
                'success' => false,
                'message' => 'Form template tidak ditemukan',
            ], 404);
        }

        $field = FormField::where('id', $fieldId)
            ->where('form_template_id', $id)
            ->first();

        if (!$field) {
            return response()->json([
                'success' => false,
                'message' => 'Field tidak ditemukan di form ini',
            ], 404);
        }

        // cek apakah field ini dari katalog — kalau iya, tolak penghapusan
        if ($field->is_kpi_field) {
            return response()->json([
                'success' => false,
                'message' => 'Field ini tidak bisa dihapus karena merupakan field utama KPI dari katalog',
            ], 422);
        }

        $field->delete();

        return response()->json([
            'success' => true,
            'message' => 'Field berhasil dihapus dari form',
        ]);
    }
}