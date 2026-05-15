<?php

namespace App\Swagger;

/**
 * @OA\Get(
 * path="/api/unit-bisnis",
 * summary="Get semua unit bisnis",
 * tags={"Unit Bisnis"},
 * security={{"bearerAuth":{}}},
 * @OA\Response(
 * response=200,
 * description="List unit bisnis",
 * @OA\JsonContent(
 * @OA\Property(property="data", type="array",
 * @OA\Items(
 * @OA\Property(property="id", type="integer", example=3),
 * @OA\Property(property="kategori_id", type="integer", example=2),
 * @OA\Property(property="nama", type="string", example="Little Bali Villa"),
 * @OA\Property(property="deskripsi", type="string", nullable=true),
 * @OA\Property(property="logo", type="string", nullable=true),
 * @OA\Property(property="is_active", type="boolean", example=true),
 * @OA\Property(property="logo_url", type="string", example="http://103.253.213.207/storage/photos/default.jpeg"),
 * @OA\Property(property="kategori", type="object",
 * @OA\Property(property="id", type="integer", example=2),
 * @OA\Property(property="nama", type="string", example="Properti Management")
 * )
 * )
 * )
 * )
 * ),
 * @OA\Response(response=401, description="Unauthenticated")
 * )
 *
 * @OA\Get(
 * path="/api/unit-bisnis/{id}",
 * summary="Get detail unit bisnis",
 * tags={"Unit Bisnis"},
 * security={{"bearerAuth":{}}},
 * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 * @OA\Response(
 * response=200,
 * description="Detail unit bisnis",
 * @OA\JsonContent(
 * @OA\Property(property="data", type="object",
 * @OA\Property(property="id", type="integer", example=3),
 * @OA\Property(property="nama", type="string", example="Little Bali Villa"),
 * @OA\Property(property="logo_url", type="string", example="http://103.253.213.207/storage/photos/default.jpeg")
 * )
 * )
 * ),
 * @OA\Response(response=404, description="Tidak ditemukan")
 * )
 *
 * @OA\Post(
 * path="/api/unit-bisnis",
 * summary="Buat unit bisnis baru (owner only)",
 * tags={"Unit Bisnis"},
 * security={{"bearerAuth":{}}},
 * @OA\RequestBody(
 * required=true,
 * @OA\JsonContent(
 * required={"kategori_id","nama"},
 * @OA\Property(property="kategori_id", type="integer", example=1),
 * @OA\Property(property="nama", type="string", example="Unit Bisnis Baru"),
 * @OA\Property(property="deskripsi", type="string", example="Deskripsi unit bisnis"),
 * @OA\Property(property="logo", type="string", example="logo.png")
 * )
 * ),
 * @OA\Response(response=201, description="Unit bisnis berhasil dibuat"),
 * @OA\Response(response=403, description="Akses ditolak")
 * )
 *
 * @OA\Put(
 * path="/api/unit-bisnis/{id}",
 * summary="Update unit bisnis (owner only)",
 * tags={"Unit Bisnis"},
 * security={{"bearerAuth":{}}},
 * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 * @OA\RequestBody(
 * @OA\JsonContent(
 * @OA\Property(property="nama", type="string", example="Nama Baru"),
 * @OA\Property(property="is_active", type="boolean", example=true)
 * )
 * ),
 * @OA\Response(response=200, description="Unit bisnis berhasil diupdate")
 * )
 *
 * @OA\Delete(
 * path="/api/unit-bisnis/{id}",
 * summary="Hapus unit bisnis (owner only)",
 * tags={"Unit Bisnis"},
 * security={{"bearerAuth":{}}},
 * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 * @OA\Response(response=200, description="Unit bisnis berhasil dihapus")
 * )
 */
class UnitBisnisAnnotation {}