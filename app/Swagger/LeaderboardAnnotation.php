<?php

namespace App\Swagger;

/**
 * @OA\Get(
 *     path="/api/leaderboard",
 *     summary="Get ranking/leaderboard semua unit bisnis berdasarkan KPI",
 *     tags={"Leaderboard"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="bulan",
 *         in="query",
 *         required=false,
 *         description="Bulan periode (1-12), default bulan ini",
 *         @OA\Schema(type="integer", example=4)
 *     ),
 *     @OA\Parameter(
 *         name="tahun",
 *         in="query",
 *         required=false,
 *         description="Tahun periode, default tahun ini",
 *         @OA\Schema(type="integer", example=2026)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Ranking unit bisnis berhasil diambil",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(
 *                 property="periode",
 *                 type="object",
 *                 @OA\Property(property="bulan", type="integer", example=4),
 *                 @OA\Property(property="tahun", type="integer", example=2026)
 *             ),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="ranking", type="integer", example=1),
 *                     @OA\Property(property="unit_bisnis_id", type="integer", example=1),
 *                     @OA\Property(property="nama", type="string", example="Cluster de Matraman"),
 *                     @OA\Property(property="kategori", type="string", example="Properti"),
 *                     @OA\Property(property="total_kpi", type="integer", example=2),
 *                     @OA\Property(property="rata_rata_persen", type="number", example=75.5),
 *                     @OA\Property(property="status", type="string", enum={"hijau","kuning","merah"}, example="kuning"),
 *                     @OA\Property(
 *                         property="detail_kpi",
 *                         type="array",
 *                         @OA\Items(
 *                             @OA\Property(property="kpi_template_id", type="integer", example=2),
 *                             @OA\Property(property="nama_kpi", type="string", example="Closing Deal"),
 *                             @OA\Property(property="target", type="number", example=10),
 *                             @OA\Property(property="realisasi", type="number", example=3),
 *                             @OA\Property(property="persentase", type="number", example=30),
 *                             @OA\Property(property="status", type="string", example="merah")
 *                         )
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 */
class LeaderboardAnnotation {}