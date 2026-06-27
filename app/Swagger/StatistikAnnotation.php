<?php

namespace App\Swagger;

class StatistikAnnotation
{
    /**
     * @OA\Get(
     * path="/api/statistik/aktivitas",
     * summary="Get daftar aktivitas submission terbaru (owner only)",
     * tags={"Statistik & KPI Master"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="tanggal", in="query", required=false, description="Filter by tanggal (format: Y-m-d)", @OA\Schema(type="string", example="2026-06-25")),
     * @OA\Parameter(name="unit_bisnis_id", in="query", required=false, description="Filter by unit bisnis", @OA\Schema(type="integer", example=1)),
     * @OA\Parameter(name="user_id", in="query", required=false, description="Filter by karyawan tertentu", @OA\Schema(type="integer", example=4)),
     * @OA\Parameter(name="per_page", in="query", required=false, description="Jumlah data per halaman (default: 20)", @OA\Schema(type="integer", example=20)),
     * @OA\Response(
     * response=200,
     * description="Daftar aktivitas submission dengan pagination",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="boolean", example=true),
     * @OA\Property(property="data", type="object",
     * @OA\Property(property="current_page", type="integer", example=1),
     * @OA\Property(property="data", type="array",
     * @OA\Items(
     * @OA\Property(property="id", type="integer", example=78),
     * @OA\Property(property="form_template_id", type="integer", example=38),
     * @OA\Property(property="unit_bisnis_id", type="integer", example=1),
     * @OA\Property(property="user_id", type="integer", example=4),
     * @OA\Property(property="created_at", type="string", example="2026-06-25T03:04:22.000000Z")
     * )
     * ),
     * @OA\Property(property="total", type="integer", example=12),
     * @OA\Property(property="per_page", type="integer", example=20)
     * )
     * )
     * ),
     * @OA\Response(response=401, description="Unauthenticated"),
     * @OA\Response(response=403, description="Akses ditolak")
     * )
     */
    public function aktivitas() {}

    /**
     * @OA\Get(
     * path="/api/statistik/aktivitas/trend",
     * summary="Get tren jumlah submission per hari dalam satu periode (owner only)",
     * tags={"Statistik & KPI Master"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="bulan", in="query", required=false, description="Bulan yang ingin dilihat (default: periode aktif)", @OA\Schema(type="integer", example=6)),
     * @OA\Parameter(name="tahun", in="query", required=false, description="Tahun yang ingin dilihat (default: periode aktif)", @OA\Schema(type="integer", example=2026)),
     * @OA\Response(
     * response=200,
     * description="Data tren submission harian",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="boolean", example=true),
     * @OA\Property(property="meta", type="object",
     * @OA\Property(property="bulan", type="integer", example=6),
     * @OA\Property(property="tahun", type="integer", example=2026),
     * @OA\Property(property="range_start", type="string", example="2026-05-26"),
     * @OA\Property(property="range_end", type="string", example="2026-06-25")
     * ),
     * @OA\Property(property="data", type="object",
     * @OA\Property(property="trend_harian", type="array",
     * @OA\Items(
     * @OA\Property(property="tanggal", type="string", example="2026-06-25"),
     * @OA\Property(property="jumlah", type="integer", example=3)
     * )
     * ),
     * @OA\Property(property="breakdown_per_unit", type="array",
     * @OA\Items(
     * @OA\Property(property="unit_bisnis_id", type="integer", example=1),
     * @OA\Property(property="nama_unit_bisnis", type="string", example="Cluster de Matraman"),
     * @OA\Property(property="jumlah", type="integer", example=5)
     * )
     * ),
     * @OA\Property(property="total_submission", type="integer", example=12)
     * )
     * )
     * ),
     * @OA\Response(response=401, description="Unauthenticated"),
     * @OA\Response(response=403, description="Akses ditolak")
     * )
     */
    public function trend() {}
}