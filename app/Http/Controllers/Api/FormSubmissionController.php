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
    // GET semua submission
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

    // GET detail 1 submission
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

    // GET semua submission berdasarkan form template tertentu
    public function byForm(Request $request, $formTemplateId)
    {
        $user = $request->user();

        $query = FormSubmission::with(['user', 'values.formField'])
            ->where('form_template_id', $formTemplateId);

        if ($user->role === 'karyawan' || $user->role === 'manajer') {
            $query->where('unit_bisnis_id', $user->unit_bisnis_id);
        }

        $data = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }

    // POST karyawan submit form
    public function store(Request $request)
    {
        $request->validate([
            'form_template_id'          => 'required|exists:form_templates,id',
            'answers'                   => 'required|array|min:1',
            'answers.*.form_field_id'   => 'required|exists:form_fields,id',
            'answers.*.nilai'           => 'nullable|string',
        ]);

        $user = $request->user();

        // Load formFields beserta kpiTemplate & kpiJenis sekaligus
        // kpiJenis dibutuhkan untuk baca formula_type
        $formTemplate = FormTemplate::with([
            'formFields.kpiTemplate.kpiJenis'
        ])->find($request->form_template_id);

        if (!$formTemplate->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Form template ini sudah tidak aktif',
            ], 422);
        }

        // Karyawan hanya bisa submit form unit bisnis sendiri
        if ($user->role === 'karyawan' || $user->role === 'manajer') {
            if ($formTemplate->unit_bisnis_id != $user->unit_bisnis_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak bisa submit form unit bisnis lain',
                ], 403);
            }
        }

        // Validasi field wajib harus diisi
        $answersMap = collect($request->answers)->keyBy('form_field_id');

        foreach ($formTemplate->formFields as $field) {
            if ($field->wajib) {
                $answer = $answersMap->get($field->id);
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
            // Buat submission
            $submission = FormSubmission::create([
                'form_template_id' => $formTemplate->id,
                'unit_bisnis_id'   => $formTemplate->unit_bisnis_id,
                'user_id'          => $user->id,
            ]);

            // Simpan semua jawaban
            foreach ($request->answers as $answer) {
                FormSubmissionValue::create([
                    'form_submission_id' => $submission->id,
                    'form_field_id'      => $answer['form_field_id'],
                    'nilai'              => $answer['nilai'],
                ]);
            }

            // AUTO UPDATE REALISASI KPI dengan formula_type
            $this->updateRealisasiKpi($formTemplate, $answersMap);

            DB::commit();

            $submission->load([
                'formTemplate',
                'unitBisnis',
                'user',
                'values.formField.kpiTemplate'
            ]);

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

    // DELETE submission (owner only)
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

    /**
     * AUTO UPDATE REALISASI KPI
     * 
     * BERUBAH dari v1:
     * Sebelumnya selalu akumulasi (realisasi += nilai_input) untuk semua KPI
     * Sekarang bercabang berdasarkan formula_type dari kpi_jenis:
     * 
     * sum        → akumulasi (+=) — untuk Revenue, Malam Terjual, Volume Gas
     * average    → hitung ulang rata-rata dari semua submission periode ini
     * count      → tambah 1 per submission, nilai input diabaikan
     * last_value → timpa langsung dengan nilai terbaru — untuk Tingkat Hunian
     */
    private function updateRealisasiKpi($formTemplate, $answersMap)
    {
        $periode = PeriodeHelper::hitungPeriode();

        foreach ($formTemplate->formFields as $field) {
            // Hanya proses field yang terhubung ke KPI
            if (!$field->kpi_template_id) continue;

            // Hanya field bertipe number yang bisa jadi nilai KPI
            if ($field->tipe !== 'number') continue;

            // Ambil jawaban user untuk field ini
            $answer = $answersMap->get($field->id);

            // Cari KPI period yang sesuai
            $kpiPeriod = KpiPeriod::where('unit_bisnis_id', $formTemplate->unit_bisnis_id)
                ->where('kpi_template_id', $field->kpi_template_id)
                ->where('periode_bulan', $periode['bulan'])
                ->where('periode_tahun', $periode['tahun'])
                ->first();

            if (!$kpiPeriod) continue;

            // Ambil formula_type dari kpi_jenis lewat relasi kpiTemplate
            $formulaType = $field->kpiTemplate?->kpiJenis?->formula_type ?? 'sum';

            // Cabang berdasarkan formula_type
            switch ($formulaType) {
                case 'sum':
                    // Akumulasi — dijumlahkan setiap ada submission baru
                    // Contoh: Revenue, Malam Terjual, Volume Gas
                    if (!$answer || is_null($answer['nilai'])) continue 2;
                    $kpiPeriod->realisasi += (float) $answer['nilai'];
                    break;

                case 'average':
                    // Rata-rata dari semua submission periode ini
                    // Hitung ulang dari semua nilai yang sudah masuk
                    if (!$answer || is_null($answer['nilai'])) continue 2;
                    $nilaiInput = (float) $answer['nilai'];

                    // Ambil semua nilai submission untuk field ini di periode yang sama
                    $semuaNilai = FormSubmissionValue::whereHas('formSubmission', function ($q) use ($formTemplate, $periode) {
                            $q->where('unit_bisnis_id', $formTemplate->unit_bisnis_id)
                              ->whereBetween('created_at', [
                                  now()->setMonth($periode['bulan'])->setYear($periode['tahun'])->startOfMonth(),
                                  now()->setMonth($periode['bulan'])->setYear($periode['tahun'])->endOfMonth(),
                              ]);
                        })
                        ->where('form_field_id', $field->id)
                        ->whereNotNull('nilai')
                        ->pluck('nilai')
                        ->map(fn($n) => (float) $n)
                        ->push($nilaiInput); // tambahkan nilai yang baru disubmit

                    $kpiPeriod->realisasi = round($semuaNilai->avg(), 2);
                    break;

                case 'count':
                    // Hitung jumlah submission — nilai input diabaikan
                    // Contoh: Jumlah Pelanggan, Jumlah Layanan
                    $kpiPeriod->realisasi += 1;
                    break;

                case 'last_value':
                    // Timpa langsung dengan nilai terbaru — tidak diakumulasi
                    // Contoh: Tingkat Hunian (snapshot harian)
                    if (!$answer || is_null($answer['nilai'])) continue 2;
                    $kpiPeriod->realisasi = (float) $answer['nilai'];
                    break;

                default:
                    // Fallback ke sum kalau formula_type tidak dikenali
                    if (!$answer || is_null($answer['nilai'])) continue 2;
                    $kpiPeriod->realisasi += (float) $answer['nilai'];
                    break;
            }

            // Hitung status merah/kuning/hijau otomatis
            $kpiPeriod->status = $kpiPeriod->hitungStatus();
            $kpiPeriod->save();
        }
    }
}