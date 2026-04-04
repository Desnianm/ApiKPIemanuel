<?php

namespace App\Swagger;

/**
 * @OA\Get(
 *     path="/api/unit-bisnis",
 *     summary="Get semua unit bisnis",
 *     tags={"Unit Bisnis"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(response=200, description="List unit bisnis"),
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 *
 * @OA\Get(
 *     path="/api/unit-bisnis/{id}",
 *     summary="Get detail unit bisnis",
 *     tags={"Unit Bisnis"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Detail unit bisnis"),
 *     @OA\Response(response=404, description="Tidak ditemukan")
 * )
 *
 * @OA\Get(
 *     path="/api/kategori-unit-bisnis",
 *     summary="Get semua kategori unit bisnis",
 *     tags={"Unit Bisnis"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(response=200, description="List kategori unit bisnis")
 * )
 *
 * @OA\Post(
 *     path="/api/unit-bisnis",
 *     summary="Buat unit bisnis baru (owner only)",
 *     tags={"Unit Bisnis"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"kategori_id","nama"},
 *             @OA\Property(property="kategori_id", type="integer", example=1),
 *             @OA\Property(property="nama", type="string", example="Unit Bisnis Baru"),
 *             @OA\Property(property="deskripsi", type="string", example="Deskripsi unit bisnis"),
 *             @OA\Property(property="logo", type="string", example="logo.png")
 *         )
 *     ),
 *     @OA\Response(response=201, description="Unit bisnis berhasil dibuat"),
 *     @OA\Response(response=403, description="Akses ditolak")
 * )
 *
 * @OA\Put(
 *     path="/api/unit-bisnis/{id}",
 *     summary="Update unit bisnis (owner only)",
 *     tags={"Unit Bisnis"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             @OA\Property(property="nama", type="string", example="Nama Baru"),
 *             @OA\Property(property="is_active", type="boolean", example=true)
 *         )
 *     ),
 *     @OA\Response(response=200, description="Unit bisnis berhasil diupdate"),
 *     @OA\Response(response=403, description="Akses ditolak")
 * )
 *
 * @OA\Delete(
 *     path="/api/unit-bisnis/{id}",
 *     summary="Hapus unit bisnis (owner only)",
 *     tags={"Unit Bisnis"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Unit bisnis berhasil dihapus"),
 *     @OA\Response(response=403, description="Akses ditolak")
 * )
 */
class UnitBisnisAnnotation {}