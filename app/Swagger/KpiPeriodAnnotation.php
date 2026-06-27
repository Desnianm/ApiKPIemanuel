<?php

namespace App\Swagger;

class KpiPeriodAnnotation
{
    /**
     * @OA\Get(
     * path="/api/kpi-periods",
     * summary="Get semua KPI period",
     * tags={"KPI Period"},
     * security={{"bearerAuth":{}}},
     * @OA\Response(response=200, description="List KPI period")
     * )
     */
    public function index() {}

    /**
     * @OA\Get(
     * path="/api/kpi-periods/summary",
     * summary="Get summary & ranking KPI semua unit bisnis",
     * tags={"KPI Period"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="bulan", in="query", required=true, @OA\Schema(type="integer", example=3)),
     * @OA\Parameter(name="tahun", in="query", required=true, @OA\Schema(type="integer", example=2026)),
     * @OA\Response(response=200, description="Summary KPI")
     * )
     */
    public function summary() {}

    /**
     * @OA\Get(
     * path="/api/kpi-periods/unit-bisnis/{unitBisnisId}",
     * summary="Get KPI period by unit bisnis & periode",
     * tags={"KPI Period"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="unitBisnisId", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Parameter(name="bulan", in="query", required=true, @OA\Schema(type="integer", example=3)),
     * @OA\Parameter(name="tahun", in="query", required=true, @OA\Schema(type="integer", example=2026)),
     * @OA\Response(response=200, description="List KPI period by unit bisnis")
     * )
     */
    public function byUnitBisnis() {}

    /**
     * @OA\Post(
     * path="/api/kpi-periods",
     * summary="Buat KPI period baru (owner only)",
     * tags={"KPI Period"},
     * security={{"bearerAuth":{}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"unit_bisnis_id","kpi_template_id","periode_bulan","periode_tahun","target","threshold_hijau","threshold_kuning"},
     * @OA\Property(property="unit_bisnis_id", type="integer", example=1),
     * @OA\Property(property="kpi_template_id", type="integer", example=1),
     * @OA\Property(property="periode_bulan", type="integer", example=3),
     * @OA\Property(property="periode_tahun", type="integer", example=2026),
     * @OA\Property(property="target", type="number", example=70000000),
     * @OA\Property(property="threshold_hijau", type="number", example=65),
     * @OA\Property(property="threshold_kuning", type="number", example=56)
     * )
     * ),
     * @OA\Response(response=201, description="KPI period berhasil dibuat"),
     * @OA\Response(response=403, description="Akses ditolak")
     * )
     */
    public function store() {}

    /**
     * @OA\Put(
     * path="/api/kpi-periods/{id}/realisasi",
     * summary="Update realisasi KPI",
     * tags={"KPI Period"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"realisasi"},
     * @OA\Property(property="realisasi", type="number", example=35000000)
     * )
     * ),
     * @OA\Response(response=200, description="Realisasi KPI berhasil diupdate")
     * )
     */
    public function updateRealisasi() {}

    /**
     * @OA\Put(
     * path="/api/kpi-periods/{id}",
     * summary="Update target & threshold KPI (owner only)",
     * tags={"KPI Period"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(response=200, description="KPI period berhasil diupdate"),
     * @OA\Response(response=403, description="Akses ditolak")
     * )
     */
    public function update() {}

    /**
     * @OA\Delete(
     * path="/api/kpi-periods/{id}",
     * summary="Hapus KPI period (owner only)",
     * tags={"KPI Period"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(response=200, description="KPI period berhasil dihapus"),
     * @OA\Response(response=403, description="Akses ditolak")
     * )
     */
    public function destroy() {}
}