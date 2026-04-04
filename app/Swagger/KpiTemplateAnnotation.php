<?php

namespace App\Swagger;

/**
 * @OA\Get(
 *     path="/api/kpi-templates",
 *     summary="Get semua KPI template",
 *     tags={"KPI Template"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(response=200, description="List KPI template")
 * )
 *
 * @OA\Get(
 *     path="/api/kpi-templates/{id}",
 *     summary="Get detail KPI template",
 *     tags={"KPI Template"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Detail KPI template"),
 *     @OA\Response(response=404, description="Tidak ditemukan")
 * )
 *
 * @OA\Get(
 *     path="/api/kpi-templates/unit-bisnis/{unitBisnisId}",
 *     summary="Get KPI template by unit bisnis",
 *     tags={"KPI Template"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="unitBisnisId", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="List KPI template by unit bisnis")
 * )
 *
 * @OA\Post(
 *     path="/api/kpi-templates",
 *     summary="Buat KPI template baru (owner only)",
 *     tags={"KPI Template"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"unit_bisnis_id","nama","kategori","satuan"},
 *             @OA\Property(property="unit_bisnis_id", type="integer", example=1),
 *             @OA\Property(property="nama", type="string", example="Revenue Bulanan"),
 *             @OA\Property(property="kategori", type="string", enum={"sales","marketing","operasional"}),
 *             @OA\Property(property="satuan", type="string", example="rupiah"),
 *             @OA\Property(property="deskripsi", type="string", example="Total cash in per bulan")
 *         )
 *     ),
 *     @OA\Response(response=201, description="KPI template berhasil dibuat"),
 *     @OA\Response(response=403, description="Akses ditolak")
 * )
 *
 * @OA\Put(
 *     path="/api/kpi-templates/{id}",
 *     summary="Update KPI template (owner only)",
 *     tags={"KPI Template"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="KPI template berhasil diupdate"),
 *     @OA\Response(response=403, description="Akses ditolak")
 * )
 *
 * @OA\Delete(
 *     path="/api/kpi-templates/{id}",
 *     summary="Hapus KPI template (owner only)",
 *     tags={"KPI Template"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="KPI template berhasil dihapus"),
 *     @OA\Response(response=403, description="Akses ditolak")
 * )
 */
class KpiTemplateAnnotation {}