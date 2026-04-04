<?php

namespace App\Swagger;

/**
 * @OA\Get(
 *     path="/api/laporan-harian",
 *     summary="Get semua laporan harian",
 *     tags={"Laporan"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(response=200, description="List laporan harian")
 * )
 *
 * @OA\Post(
 *     path="/api/laporan-harian",
 *     summary="Input laporan harian",
 *     tags={"Laporan"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"unit_bisnis_id","tanggal","aktivitas"},
 *             @OA\Property(property="unit_bisnis_id", type="integer", example=1),
 *             @OA\Property(property="tanggal", type="string", format="date", example="2026-03-29"),
 *             @OA\Property(property="aktivitas", type="string", example="Meeting dengan calon buyer"),
 *             @OA\Property(property="leads_masuk", type="integer", example=3),
 *             @OA\Property(property="closing_deal", type="integer", example=1),
 *             @OA\Property(property="catatan", type="string", example="Catatan tambahan")
 *         )
 *     ),
 *     @OA\Response(response=201, description="Laporan harian berhasil diinput"),
 *     @OA\Response(response=422, description="Laporan sudah ada untuk tanggal ini")
 * )
 *
 * @OA\Put(
 *     path="/api/laporan-harian/{id}",
 *     summary="Update laporan harian",
 *     tags={"Laporan"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Laporan harian berhasil diupdate")
 * )
 *
 * @OA\Delete(
 *     path="/api/laporan-harian/{id}",
 *     summary="Hapus laporan harian (owner only)",
 *     tags={"Laporan"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Laporan harian berhasil dihapus"),
 *     @OA\Response(response=403, description="Akses ditolak")
 * )
 *
 * @OA\Get(
 *     path="/api/laporan-sop",
 *     summary="Get semua laporan SOP",
 *     tags={"Laporan"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(response=200, description="List laporan SOP")
 * )
 *
 * @OA\Post(
 *     path="/api/laporan-sop",
 *     summary="Input laporan SOP",
 *     tags={"Laporan"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"unit_bisnis_id","tanggal","deskripsi"},
 *             @OA\Property(property="unit_bisnis_id", type="integer", example=1),
 *             @OA\Property(property="tanggal", type="string", format="date", example="2026-03-29"),
 *             @OA\Property(property="deskripsi", type="string", example="Pengecekan kebersihan"),
 *             @OA\Property(property="foto", type="string", example="foto.jpg"),
 *             @OA\Property(property="lokasi", type="string", example="Cluster de Matraman"),
 *             @OA\Property(property="latitude", type="number", example=-6.2088),
 *             @OA\Property(property="longitude", type="number", example=106.8456)
 *         )
 *     ),
 *     @OA\Response(response=201, description="Laporan SOP berhasil diinput")
 * )
 *
 * @OA\Patch(
 *     path="/api/laporan-sop/{id}/review",
 *     summary="Review laporan SOP (owner only)",
 *     tags={"Laporan"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"status"},
 *             @OA\Property(property="status", type="string", enum={"approved","rejected"}),
 *             @OA\Property(property="catatan_owner", type="string", example="SOP sudah dijalankan dengan baik")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Laporan SOP berhasil direview"),
 *     @OA\Response(response=403, description="Akses ditolak")
 * )
 *
 * @OA\Delete(
 *     path="/api/laporan-sop/{id}",
 *     summary="Hapus laporan SOP (owner only)",
 *     tags={"Laporan"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Laporan SOP berhasil dihapus"),
 *     @OA\Response(response=403, description="Akses ditolak")
 * )
 */
class LaporanAnnotation {}