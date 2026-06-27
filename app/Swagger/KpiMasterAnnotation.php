<?php

namespace App\Swagger;

class KpiMasterAnnotation
{
    /**
     * @OA\Get(
     * path="/api/periode/aktif",
     * summary="Get periode yang sedang aktif sesuai logika cut-off tanggal 25",
     * tags={"Statistik & KPI Master"},
     * security={{"bearerAuth":{}}},
     * @OA\Response(
     * response=200,
     * description="Info periode aktif",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="boolean", example=true),
     * @OA\Property(property="data", type="object",
     * @OA\Property(property="bulan", type="integer", example=7),
     * @OA\Property(property="tahun", type="integer", example=2026),
     * @OA\Property(property="label", type="string", example="Juli 2026"),
     * @OA\Property(property="range", type="object",
     * @OA\Property(property="start", type="string", example="2026-06-26"),
     * @OA\Property(property="end", type="string", example="2026-07-25")
     * ),
     * @OA\Property(property="cutoff_info", type="string", example="Periode bergeser setiap tanggal 26")
     * )
     * )
     * ),
     * @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function periodeAktif() {}

    /**
     * @OA\Get(
     * path="/api/kpi-master/summary",
     * summary="Get agregat KPI semua unit bisnis dalam 1 periode (owner only)",
     * tags={"Statistik & KPI Master"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="bulan", in="query", required=false, description="Bulan yang ingin dilihat (default: periode aktif)", @OA\Schema(type="integer", example=6)),
     * @OA\Parameter(name="tahun", in="query", required=false, description="Tahun yang ingin dilihat (default: periode aktif)", @OA\Schema(type="integer", example=2026)),
     * @OA\Response(
     * response=200,
     * description="Agregat KPI seluruh perusahaan",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="boolean", example=true),
     * @OA\Property(property="meta", type="object",
     * @OA\Property(property="periode_bulan", type="integer", example=6),
     * @OA\Property(property="periode_tahun", type="integer", example=2026)
     * ),
     * @OA\Property(property="data", type="object",
     * @OA\Property(property="perusahaan", type="object",
     * @OA\Property(property="total_target", type="number", example=210000000),
     * @OA\Property(property="total_realisasi", type="number", example=170000000),
     * @OA\Property(property="persentase", type="number", example=80.95),
     * @OA\Property(property="jumlah_per_status", type="object",
     * @OA\Property(property="hijau", type="integer", example=1),
     * @OA\Property(property="kuning", type="integer", example=1),
     * @OA\Property(property="merah", type="integer", example=0)
     * )
     * ),
     * @OA\Property(property="breakdown_per_unit", type="array",
     * @OA\Items(
     * @OA\Property(property="total_target", type="number", example=100000000),
     * @OA\Property(property="total_realisasi", type="number", example=85000000),
     * @OA\Property(property="persentase", type="number", example=85.0),
     * @OA\Property(property="status", type="string", example="hijau"),
     * @OA\Property(property="jumlah_kpi", type="integer", example=2)
     * )
     * )
     * )
     * )
     * ),
     * @OA\Response(response=401, description="Unauthenticated"),
     * @OA\Response(response=403, description="Akses ditolak")
     * )
     */
    public function summary() {}

    /**
     * @OA\Get(
     * path="/api/kpi-master/trend",
     * summary="Get tren KPI perusahaan multi-bulan untuk grafik (owner only)",
     * tags={"Statistik & KPI Master"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="dari_bulan", in="query", required=true, description="Bulan awal rentang", @OA\Schema(type="integer", example=1)),
     * @OA\Parameter(name="dari_tahun", in="query", required=true, description="Tahun awal rentang", @OA\Schema(type="integer", example=2026)),
     * @OA\Parameter(name="sampai_bulan", in="query", required=true, description="Bulan akhir rentang", @OA\Schema(type="integer", example=6)),
     * @OA\Parameter(name="sampai_tahun", in="query", required=true, description="Tahun akhir rentang", @OA\Schema(type="integer", example=2026)),
     * @OA\Response(
     * response=200,
     * description="Data tren KPI multi-bulan",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="boolean", example=true),
     * @OA\Property(property="meta", type="object",
     * @OA\Property(property="dari", type="string", example="Jan 2026"),
     * @OA\Property(property="sampai", type="string", example="Jun 2026"),
     * @OA\Property(property="jumlah_periode", type="integer", example=6)
     * ),
     * @OA\Property(property="data", type="array",
     * @OA\Items(
     * @OA\Property(property="periode", type="string", example="Jun 2026"),
     * @OA\Property(property="bulan", type="integer", example=6),
     * @OA\Property(property="tahun", type="integer", example=2026),
     * @OA\Property(property="total_target", type="number", example=210000000),
     * @OA\Property(property="total_realisasi", type="number", example=170000000),
     * @OA\Property(property="persentase", type="number", example=80.95)
     * )
     * )
     * )
     * ),
     * @OA\Response(response=401, description="Unauthenticated"),
     * @OA\Response(response=403, description="Akses ditolak")
     * )
     */
    public function trend() {}
}