<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KpiJenis;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 * schema="KpiJenisResponse",
 * type="object",
 * title="KpiJenis Response Schema",
 * @OA\Property(property="id", type="integer", example=1),
 * @OA\Property(property="kode", type="string", example="revenue"),
 * @OA\Property(property="nama", type="string", example="Revenue"),
 * @OA\Property(property="kategori", type="string", example="keuangan"),
 * @OA\Property(property="satuan_default", type="string", example="rupiah"),
 * @OA\Property(property="formula_type", type="string",
 *     enum={"sum","average","count","last_value","minimize","range","binary"},
 *     example="sum"),
 * @OA\Property(
 * property="field_definitions",
 * type="array",
 * @OA\Items(
 * type="object",
 * @OA\Property(property="label", type="string", example="Nilai Transaksi"),
 * @OA\Property(property="tipe", type="string", example="number"),
 * @OA\Property(property="wajib", type="boolean", example=true)
 * )
 * ),
 * @OA\Property(property="nilai_min", type="number", nullable=true, example=1,
 *     description="Batas bawah skala — dipakai formula range"),
 * @OA\Property(property="nilai_max", type="number", nullable=true, example=5,
 *     description="Batas atas skala — dipakai formula range"),
 * @OA\Property(property="total_milestone", type="integer", nullable=true, example=4,
 *     description="Total milestone yang harus dicapai — dipakai formula binary"),
 * @OA\Property(property="is_capped", type="boolean", example=true,
 *     description="Apakah persentase dibatasi maksimal 100%"),
 * @OA\Property(property="deskripsi", type="string", example="Deskripsi katalog KPI", nullable=true),
 * @OA\Property(property="is_active", type="boolean", example=true),
 * @OA\Property(property="created_at", type="string", format="date-time", example="2026-06-27T10:00:00Z"),
 * @OA\Property(property="updated_at", type="string", format="date-time", example="2026-06-27T10:00:00Z")
 * )
 */
class KpiJenisController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/kpi-jenis",
     * summary="Get daftar katalog KPI (untuk dropdown admin), bisa difilter per kategori unit bisnis",
     * tags={"KPI Jenis Catalog"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     *     name="kategori_id", in="query", required=false,
     *     description="Filter KPI yang relevan untuk kategori unit bisnis tertentu (Properti/PM/UMKM). Kalau tidak diisi, tampilkan semua KPI aktif.",
     *     @OA\Schema(type="integer", example=1)
     * ),
     * @OA\Response(
     * response=200,
     * description="Daftar jenis KPI yang aktif",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="boolean", example=true),
     * @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/KpiJenisResponse"))
     * )
     * ),
     * @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(Request $request)
    {
        $request->validate([
            'kategori_id' => 'nullable|integer|exists:kategori_unit_bisnis,id',
        ]);

        $query = KpiJenis::where('is_active', true);

        // Filter opsional berdasarkan kategori unit bisnis.
        // Kalau parameter tidak dikirim, behavior tetap seperti semula
        // (balikin semua KPI aktif) — tidak breaking change.
        if ($request->filled('kategori_id')) {
            $query->whereHas('kategoriUnitBisnis', function ($q) use ($request) {
                $q->where('kategori_unit_bisnis.id', $request->kategori_id);
            });
        }

        $data = $query->orderBy('kategori')
            ->orderBy('nama')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }

    /**
     * @OA\Get(
     * path="/api/kpi-jenis/{id}",
     * summary="Get detail 1 jenis KPI dari katalog",
     * tags={"KPI Jenis Catalog"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(
     * response=200, 
     * description="Detail jenis KPI",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="boolean", example=true),
     * @OA\Property(property="data", ref="#/components/schemas/KpiJenisResponse")
     * )
     * ),
     * @OA\Response(response=404, description="Tidak ditemukan")
     * )
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
     * @OA\Post(
     * path="/api/kpi-jenis",
     * summary="Tambah jenis KPI baru ke katalog (owner only)",
     * tags={"KPI Jenis Catalog"},
     * security={{"bearerAuth":{}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"kode","nama","kategori","satuan_default","formula_type","field_definitions"},
     * @OA\Property(property="kode", type="string", example="complaint_count"),
     * @OA\Property(property="nama", type="string", example="Jumlah Komplain"),
     * @OA\Property(property="kategori", type="string",
     *     enum={"sales","operasional","keuangan","sdm","lainnya"}, example="operasional"),
     * @OA\Property(property="satuan_default", type="string",
     *     enum={"rupiah","persen","unit","malam","lainnya"}, example="unit"),
     * @OA\Property(property="formula_type", type="string",
     *     enum={"sum","average","count","last_value","minimize","range","binary"},
     *     example="minimize"),
     * @OA\Property(
     * property="field_definitions",
     * type="array",
     * @OA\Items(
     * type="object",
     * @OA\Property(property="label", type="string", example="Jumlah Komplain"),
     * @OA\Property(property="tipe", type="string", example="number"),
     * @OA\Property(property="wajib", type="boolean", example=true)
     * )
     * ),
     * @OA\Property(property="nilai_min", type="number", nullable=true, example=null,
     *     description="Wajib diisi kalau formula_type = range"),
     * @OA\Property(property="nilai_max", type="number", nullable=true, example=null,
     *     description="Wajib diisi kalau formula_type = range, harus lebih besar dari nilai_min"),
     * @OA\Property(property="total_milestone", type="integer", nullable=true, example=null,
     *     description="Wajib diisi kalau formula_type = binary"),
     * @OA\Property(property="is_capped", type="boolean", example=true,
     *     description="Batasi persentase maksimal 100%, default true"),
     * @OA\Property(property="deskripsi", type="string", nullable=true,
     *     example="Deskripsi tambahan KPI")
     * )
     * ),
     * @OA\Response(response=201, description="Jenis KPI berhasil ditambahkan"),
     * @OA\Response(response=403, description="Akses ditolak")
     * )
     */
    public function store(Request $request)
    {
       $request->validate([
        'kode'              => 'required|string|max:50|unique:kpi_jenis,kode',
        'nama'              => 'required|string|max:150',
        'kategori'          => 'required|in:sales,operasional,keuangan,sdm,lainnya',
        'satuan_default'    => 'required|in:rupiah,persen,unit,malam,lainnya',
        'formula_type'      => 'required|in:sum,average,count,last_value,minimize,range,binary',
        'field_definitions' => 'required|array|min:1',
        'field_definitions.*.label' => 'required|string',
        'field_definitions.*.tipe'  => 'required|in:text,number,date,select,textarea',
        'field_definitions.*.wajib' => 'boolean',
        'nilai_min'         => 'nullable|numeric',  // wajib kalau formula range
        'nilai_max'         => 'nullable|numeric|gt:nilai_min', // harus lebih besar dari min
        'total_milestone'   => 'nullable|integer|min:1', // wajib kalau formula binary
        'is_capped'         => 'boolean',
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
     * @OA\Put(
     * path="/api/kpi-jenis/{id}",
     * summary="Update jenis KPI di katalog (owner only)",
     * tags={"KPI Jenis Catalog"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * @OA\Property(property="nama", type="string", example="Revenue Updated")
     * )
     * ),
     * @OA\Response(response=200, description="Jenis KPI berhasil diupdate"),
     * @OA\Response(response=403, description="Akses ditolak")
     * )
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
        'formula_type'      => 'sometimes|in:sum,average,count,last_value,minimize,range,binary',
        'field_definitions' => 'sometimes|array|min:1',
        'field_definitions.*.label' => 'required_with:field_definitions|string',
        'field_definitions.*.tipe'  => 'required_with:field_definitions|in:text,number,date,select,textarea',
        'field_definitions.*.wajib' => 'boolean',
        'nilai_min'         => 'nullable|numeric',
        'nilai_max'         => 'nullable|numeric|gt:nilai_min',
        'total_milestone'   => 'nullable|integer|min:1',
        'is_capped'         => 'boolean',
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
     * @OA\Patch(
     * path="/api/kpi-jenis/{id}/toggle",
     * summary="Aktifkan/nonaktifkan jenis KPI (owner only)",
     * tags={"KPI Jenis Catalog"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(response=200, description="Status jenis KPI berhasil diubah"),
     * @OA\Response(response=403, description="Akses ditolak")
     * )
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