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
    // get semua submission
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

    // get detail 1 submission
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

    // get semua submission berdasarkan form template tertentu
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

    // post karyawan submit form
    public function store(Request $request)
    {
        $request->validate([
            'form_template_id'          => 'required|exists:form_templates,id',
            'answers'                   => 'required|array|min:1',
            'answers.*.form_field_id'   => 'required|exists:form_fields,id',
            'answers.*.nilai'           => 'nullable|string',
        ]);

        $user = $request->user();

        // load formFields beserta kpiTemplate & kpiJenis sekaligus
        $formTemplate = FormTemplate::with([
            'formFields.kpiTemplate.kpiJenis'
        ])->find($request->form_template_id);

        if (!$formTemplate->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Form template ini sudah tidak aktif',
            ], 422);
        }

        // karyawan cuma bisa submit form unit bisnis sendiri
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
            // membuat submission
            $submission = FormSubmission::create([
                'form_template_id' => $formTemplate->id,
                'unit_bisnis_id'   => $formTemplate->unit_bisnis_id,
                'user_id'          => $user->id,
            ]);

            // simpan semua jawaban
            foreach ($request->answers as $answer) {
                FormSubmissionValue::create([
                    'form_submission_id' => $submission->id,
                    'form_field_id'      => $answer['form_field_id'],
                    'nilai'              => $answer['nilai'],
                ]);
            }

            // auto up realisasi KPI dengan formula_type
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

    // delete submission cuma owner
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

    
    private function updateRealisasiKpi($formTemplate, $answersMap)
    {
        $periode = PeriodeHelper::hitungPeriode();

        foreach ($formTemplate->formFields as $field) {
            // hanya proses field yang terhubung ke KPI
            if (!$field->kpi_template_id) continue;

            // hanya field angka yang bisa jadi nilai KPI
            if ($field->tipe !== 'number') continue;

            // ambil jawaban user untuk field ini
            $answer = $answersMap->get($field->id);

            // cari KPI period yang sesuai
            $kpiPeriod = KpiPeriod::where('unit_bisnis_id', $formTemplate->unit_bisnis_id)
                ->where('kpi_template_id', $field->kpi_template_id)
                ->where('periode_bulan', $periode['bulan'])
                ->where('periode_tahun', $periode['tahun'])
                ->first();

            if (!$kpiPeriod) continue;

            // ambil formula_type dari kpi_jenis
            $kpiJenis = $field->kpiTemplate?->kpiJenis;
            $formulaType = $kpiJenis?->formula_type ?? 'sum';

            switch ($formulaType) {

                case 'sum':
                    // dijumlahkan setiap ada submission
                    if (!$answer || is_null($answer['nilai'])) continue 2;
                    $kpiPeriod->realisasi += (float) $answer['nilai'];
                    break;

                case 'average':
                    // rata rata dari semua submission periode ini
                    if (!$answer || is_null($answer['nilai'])) continue 2;
                    $nilaiInput = (float) $answer['nilai'];

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
                        ->push($nilaiInput);

                    $kpiPeriod->realisasi = round($semuaNilai->avg(), 2);
                    break;

                case 'count':
                    // hitung jumlah submission, nilai input diabaikan
                    $kpiPeriod->realisasi += 1;
                    break;

                case 'last_value':
                    // timpa langsung dengan nilai terbaru
                    if (!$answer || is_null($answer['nilai'])) continue 2;
                    $kpiPeriod->realisasi = (float) $answer['nilai'];
                    break;

                case 'minimize':
                    // semakin kecil realisasi semakin bagus
                    // realisasi disimpan apa adanya, persentase dihitung saat ditampilkan
                    if (!$answer || is_null($answer['nilai'])) continue 2;
                    $kpiPeriod->realisasi += (float) $answer['nilai'];
                    break;

                case 'range':
                    // berbasis skala min-max
                    // pakai last value karena rating selalu ditimpa nilai terbaru
                    if (!$answer || is_null($answer['nilai'])) continue 2;
                    $kpiPeriod->realisasi = (float) $answer['nilai'];
                    break;

                case 'binary':
                    $kpiPeriod->realisasi += 1;
                    break;

                default:
                    if (!$answer || is_null($answer['nilai'])) continue 2;
                    $kpiPeriod->realisasi += (float) $answer['nilai'];
                    break;
            }

            // hitung status merah/kuning/hijau
            // untuk minimize: persentase dihitung terbalik
            if ($formulaType === 'minimize') {
                $kpiPeriod->status = $this->hitungStatusMinimize($kpiPeriod);
            } elseif ($formulaType === 'range' && $kpiJenis) {
                $kpiPeriod->status = $this->hitungStatusRange($kpiPeriod, $kpiJenis);
            } elseif ($formulaType === 'binary' && $kpiJenis) {
                $kpiPeriod->status = $this->hitungStatusBinary($kpiPeriod, $kpiJenis);
            } else {
                $kpiPeriod->status = $kpiPeriod->hitungStatus();
            }

            $kpiPeriod->save();
        }
    }

    // helper hitung status untuk formula minimize
    // semakin kecil realisasi dari target = semakin bagus
    private function hitungStatusMinimize(KpiPeriod $kpiPeriod): string
    {
        if ($kpiPeriod->realisasi == 0) return 'hijau'; // realisasi 0 = sempurna
        if ($kpiPeriod->target == 0) return 'merah';

        $persentase = ($kpiPeriod->target / $kpiPeriod->realisasi) * 100;

        if ($persentase >= $kpiPeriod->threshold_hijau) return 'hijau';
        if ($persentase >= $kpiPeriod->threshold_kuning) return 'kuning';
        return 'merah';
    }

    // helper hitung status untuk formula range (skala min-max)
    private function hitungStatusRange(KpiPeriod $kpiPeriod, $kpiJenis): string
    {
        $min = $kpiJenis->nilai_min ?? 0;
        $max = $kpiJenis->nilai_max ?? 100;

        if ($max == $min) return 'merah';

        $realisasiClamped = max($min, min($kpiPeriod->realisasi, $max));
        $persentase = (($realisasiClamped - $min) / ($max - $min)) * 100;

        if ($persentase >= $kpiPeriod->threshold_hijau) return 'hijau';
        if ($persentase >= $kpiPeriod->threshold_kuning) return 'kuning';
        return 'merah';
    }

    // helper hitung status untuk formula binary (milestone)
    private function hitungStatusBinary(KpiPeriod $kpiPeriod, $kpiJenis): string
    {
        $totalMilestone = $kpiJenis->total_milestone ?? 1;

        if ($totalMilestone == 0) {
            $persentase = $kpiPeriod->realisasi >= 1 ? 100 : 0;
        } else {
            $persentase = ($kpiPeriod->realisasi / $totalMilestone) * 100;
        }

       
        if ($kpiJenis->is_capped) {
            $persentase = min($persentase, 100);
        }

        if ($persentase >= $kpiPeriod->threshold_hijau) return 'hijau';
        if ($persentase >= $kpiPeriod->threshold_kuning) return 'kuning';
        return 'merah';
    }
}