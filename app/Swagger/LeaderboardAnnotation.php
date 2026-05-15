<?php

namespace App\Swagger;

/**
 * @OA\Get(
 * path="/api/leaderboard",
 * summary="Get ranking/leaderboard semua unit bisnis berdasarkan KPI",
 * tags={"Leaderboard"},
 * security={{"bearerAuth":{}}},
 * @OA\Parameter(
 * name="bulan",
 * in="query",
 * required=false,
 * description="Bulan periode (1-12), default bulan ini",
 * @OA\Schema(type="integer", example=5)
 * ),
 * @OA\Parameter(
 * name="tahun",
 * in="query",
 * required=false,
 * description="Tahun periode, default tahun ini",
 * @OA\Schema(type="integer", example=2026)
 * ),
 * @OA\Response(
 * response=200,
 * description="Ranking unit bisnis berhasil diambil",
 * @OA\JsonContent(
 * @OA\Property(property="success", type="boolean", example=true),
 * @OA\Property(
 * property="periode",
 * type="object",
 * @OA\Property(property="bulan", type="integer", example=5),
 * @OA\Property(property="tahun", type="integer", example=2026)
 * ),
 * @OA\Property(
 * property="data",
 * type="array",
 * @OA\Items(
 * @OA\Property(property="ranking", type="integer", example=1),
 * @OA\Property(property="unit_bisnis_id", type="integer", example=1),
 * @OA\Property(property="nama", type="string", example="Cluster de Matraman"),
 * @OA\Property(property="photo_url", type="string", example="http://103.253.213.207/storage/photos/default.jpeg"),
 * @OA\Property(property="kategori", type="string", example="Properti"),
 * @OA\Property(property="total_kpi", type="integer", example=0),
 * @OA\Property(property="rata_rata_persen", type="number", example=0),
 * @OA\Property(property="status", type="string", enum={"hijau","kuning","merah"}, example="merah"),
 * @OA\Property(property="indikator", type="string", example="UNDERPERFORM"),
 * @OA\Property(
 * property="detail_kpi",
 * type="array",
 * @OA\Items()
 * )
 * )
 * )
 * )
 * ),
 * @OA\Response(response=401, description="Unauthenticated")
 * )
 */
class LeaderboardAnnotation {}