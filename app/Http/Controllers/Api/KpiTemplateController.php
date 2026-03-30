<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KpiTemplate;
use Illuminate\Http\Request;

class KpiTemplateController extends Controller
{
    // GET semua KPI template
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'owner') {
            $templates = KpiTemplate::with('unitBisnis')->get();
        } else {
            $templates = KpiTemplate::with('unitBisnis')
                ->where('unit_bisnis_id', $user->unit_bisnis_id)
                ->get();
        }

        return response()->json([
            'data' => $templates
        ], 200);
    }

    // GET detail 1 KPI template
    public function show($id)
    {
        $template = KpiTemplate::with('unitBisnis')->find($id);

        if (!$template) {
            return response()->json([
                'message' => 'KPI template tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'data' => $template
        ], 200);
    }

    // GET KPI template by unit bisnis
    public function byUnitBisnis($unitBisnisId)
    {
        $templates = KpiTemplate::with('unitBisnis')
            ->where('unit_bisnis_id', $unitBisnisId)
            ->get();

        return response()->json([
            'data' => $templates
        ], 200);
    }

    // POST buat KPI template baru (owner only)
    public function store(Request $request)
    {
        $request->validate([
            'unit_bisnis_id' => 'required|exists:unit_bisnis,id',
            'nama'           => 'required|string|max:150',
            'kategori'       => 'required|in:sales,marketing,operasional',
            'satuan'         => 'required|string|max:50',
            'deskripsi'      => 'nullable|string',
        ]);

        $template = KpiTemplate::create($request->all());

        return response()->json([
            'message' => 'KPI template berhasil dibuat.',
            'data'    => $template
        ], 201);
    }

    // PUT update KPI template (owner only)
    public function update(Request $request, $id)
    {
        $template = KpiTemplate::find($id);

        if (!$template) {
            return response()->json([
                'message' => 'KPI template tidak ditemukan.'
            ], 404);
        }

        $request->validate([
            'unit_bisnis_id' => 'sometimes|exists:unit_bisnis,id',
            'nama'           => 'sometimes|string|max:150',
            'kategori'       => 'sometimes|in:sales,marketing,operasional',
            'satuan'         => 'sometimes|string|max:50',
            'deskripsi'      => 'nullable|string',
        ]);

        $template->update($request->all());

        return response()->json([
            'message' => 'KPI template berhasil diupdate.',
            'data'    => $template
        ], 200);
    }

    // DELETE KPI template (owner only)
    public function destroy($id)
    {
        $template = KpiTemplate::find($id);

        if (!$template) {
            return response()->json([
                'message' => 'KPI template tidak ditemukan.'
            ], 404);
        }

        $template->delete();

        return response()->json([
            'message' => 'KPI template berhasil dihapus.'
        ], 200);
    }
}