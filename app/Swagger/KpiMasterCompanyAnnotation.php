<?php

namespace App\Swagger;

class KpiMasterCompanyAnnotation
{
    /**
     * @OA\Get(
     * path="/api/kpi-master/company",
     * summary="Get skor KPI Master tingkat company hasil aggregation 2 level: Kategori -> Unit (owner only)",
     * tags={"KPI Master Company"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="bulan", in="query", required=false, description="Bulan yang ingin dilihat (default: periode aktif)", @OA\Schema(type="integer", example=8)),
     * @OA\Parameter(name="tahun", in="query", required=false, description="Tahun yang ingin dilihat (default: periode aktif)", @OA\Schema(type="integer", example=2026)),
     * @OA\Response(
     * response=200,
     * description="Skor company beserta breakdown kategori dan unit",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="boolean", example=true),
     * @OA\Property(property="data", type="object",
     * @OA\Property(property="bulan", type="integer", example=8),
     * @OA\Property(property="tahun", type="integer", example=2026),
     * @OA\Property(property="skor_company", type="number", example=3.4),
     * @OA\Property(property="kategori", type="array",
     * @OA\Items(
     * @OA\Property(property="kategori_id", type="integer", example=1),
     * @OA\Property(property="nama_kategori", type="string", example="Properti"),
     * @OA\Property(property="rata_rata", type="number", example=8.5),
     * @OA\Property(property="bobot_persen", type="number", example=40),
     * @OA\Property(property="kontribusi", type="number", example=3.4),
     * @OA\Property(property="unit", type="array",
     * @OA\Items(
     * @OA\Property(property="unit_id", type="integer", example=1),
     * @OA\Property(property="nama_unit", type="string", example="Cluster de Matraman"),
     * @OA\Property(property="skor", type="number", example=42.5)
     * )
     * )
     * )
     * )
     * )
     * )
     * ),
     * @OA\Response(response=401, description="Unauthenticated"),
     * @OA\Response(response=403, description="Akses ditolak")
     * )
     */
    public function company() {}
}