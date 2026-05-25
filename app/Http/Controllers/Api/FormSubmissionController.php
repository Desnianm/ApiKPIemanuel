<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FormSubmission;
use App\Models\FormSubmissionValue;
use App\Models\FormTemplate;
use App\Models\FormField;
use App\Models\KpiPeriod;
use App\Helpers\PeriodeHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FormSubmissionController extends Controller
{
    //get untuk owner (lihat semua) dan karyawan (hanya lihat divisi mereka sendiri)
    public function index(Request $request)
    {
        $user = $request->user();

        $query = FormSubmission::with([
            'formTemplate',
            'unitBisnis',
            'user',
            'values.formField'
        ]);

        if ($user->role === 'karyawan' || $user->role === 'manajer') {
            $query->where('unit_bisnis_id', $user->unit_bisnis_id);
        }

        $data = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }

    //get detail 1 submission dan jawabannya 
    public function show($id)
    {
        $submission = FormSubmission::with([
            'formTemplate',
            'unitBisnis',
            'user',
            'values.formField.kpiTemplate'
        ])->find($id);

        if (!$submission) {
            return response()->json([
                'success' => false,
                'message' => 'Submission tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $submission,
        ]);
    }

    //lihat semua submission berdasarkan form template tertentu 
    public function byForm(Request $request, $formTemplateId)
    {
        $user = $request->user();

        $query = FormSubmission::with(['user', 'values.formField'])
            ->where('form_template_id', $formTemplateId);

        // Karyawan hanya bisa lihat submission unit bisnis sendiri
        if ($user->role === 'karyawan' || $user->role === 'manajer') {
            $query->where('unit_bisnis_id', $user->unit_bisnis_id);
        }

        $data = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }

   //post karyawan submission
    public function store(Request $request)
    {
        $request->validate([
            'form_template_id'          => 'required|exists:form_templates,id',
            'answers'                   => 'required|array|min:1',
            'answers.*.form_field_id'   => 'required|exists:form_fields,id',
            'answers.*.nilai'           => 'nullable|string',
        ]);

        $user = $request->user();

        // cek form template ada dan aktif
        $formTemplate = FormTemplate::with('formFields.kpiTemplate')
            ->find($request->form_template_id);

        if (!$formTemplate->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Form template ini sudah tidak aktif',
            ], 422);
        }

        // cek karyawan hanya bisa submit form unit bisnis sendiri
        if ($user->role === 'karyawan' || $user->role === 'manajer') {
            if ($formTemplate->unit_bisnis_id != $user->unit_bisnis_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak bisa submit form unit bisnis lain',
                ], 403);
            }
        }

        // validasi field wajib harus diisi
        $answersMap = collect($request->answers)->keyBy('form_field_id');

        foreach ($formTemplate->formFields as $field) {
            if ($field->wajib) {
                $answer = $answersMap->get($field->id);
                // field wajib tapi tidak ada di answers, atau nilainya kosong
                if (!$answer || is_null($answer['nilai']) || $answer['nilai'] === '') {
                    return response()->json([
                        'success' => false,
                        'message' => "Field '{$field->label}' wajib diisi",
                    ], 422);
                }
            }
        }

        DB::beginTransaction();
        try {
            // buat submission
            $submission = FormSubmission::create([
                'form_template_id' => $formTemplate->id,
                'unit_bisnis_id'   => $formTemplate->unit_bisnis_id,
                'user_id'          => $user->id,
            ]);

            // menyimpan semua jawaban
            foreach ($request->answers as $answer) {
                FormSubmissionValue::create([
                    'form_submission_id' => $submission->id,
                    'form_field_id'      => $answer['form_field_id'],
                    'nilai'              => $answer['nilai'],
                ]);
            }

            // AUTO UPDATE REALISASI KPI
            // Cari field mana yang punya kpi_template_id (field yang terhubung ke KPI)
            $this->updateRealisasiKpi($formTemplate, $answersMap);

            DB::commit();

            $submission->load(['formTemplate', 'unitBisnis', 'user', 'values.formField.kpiTemplate']);

            return response()->json([
                'success' => true,
                'message' => 'Form berhasil disubmit',
                'data'    => $submission,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal submit form: ' . $e->getMessage(),
            ], 500);
        }
    }

    //delete (owner only)
    public function destroy($id)
    {
        $submission = FormSubmission::find($id);

        if (!$submission) {
            return response()->json([
                'success' => false,
                'message' => 'Submission tidak ditemukan',
            ], 404);
        }

        $submission->delete();

        return response()->json([
            'success' => true,
            'message' => 'Submission berhasil dihapus',
        ]);
    }

    //auto update realisasi kpi 
    private function updateRealisasiKpi($formTemplate, $answersMap)
    {
        // hitung periode bulan ini pakai PeriodeHelper yang sudah ada
        $periode = PeriodeHelper::hitungPeriode();

        foreach ($formTemplate->formFields as $field) {
            // hanya  proses field yang terhubung ke KPI template
            if (!$field->kpi_template_id) continue;

            // Hanya field bertipe number yang bisa jadi nilai KPI
            if ($field->tipe !== 'number') continue;

            // Ambil jawaban user untuk field ini
            $answer = $answersMap->get($field->id);
            if (!$answer || is_null($answer['nilai'])) continue;

            $nilaiInput = (float) $answer['nilai'];

            // Cari KPI period yang sesuai (unit bisnis + kpi template + periode bulan ini)
            $kpiPeriod = KpiPeriod::where('unit_bisnis_id', $formTemplate->unit_bisnis_id)
                ->where('kpi_template_id', $field->kpi_template_id)
                ->where('periode_bulan', $periode['bulan'])
                ->where('periode_tahun', $periode['tahun'])
                ->first();

            if (!$kpiPeriod) continue;

            // Tambahkan nilai input ke realisasi yang sudah ada (akumulasi)
            $realisasiBaru = $kpiPeriod->realisasi + $nilaiInput;

            // Hitung status merah/kuning/hijau otomatis
            $kpiPeriod->realisasi = $realisasiBaru;
            $statusBaru = $kpiPeriod->hitungStatus();

            // Update ke database
            $kpiPeriod->update([
                'realisasi' => $realisasiBaru,
                'status'    => $statusBaru,
            ]);
        }
    }
}