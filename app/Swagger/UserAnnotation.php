<?php

namespace App\Swagger;

/**
 * @OA\Get(
 *     path="/api/users",
 *     summary="Get semua user (owner only)",
 *     tags={"User Management"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(response=200, description="List user"),
 *     @OA\Response(response=403, description="Akses ditolak")
 * )
 *
 * @OA\Get(
 *     path="/api/users/{id}",
 *     summary="Get detail user (owner only)",
 *     tags={"User Management"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Detail user"),
 *     @OA\Response(response=404, description="Tidak ditemukan")
 * )
 *
 * @OA\Post(
 *     path="/api/users",
 *     summary="Buat user baru (owner only)",
 *     tags={"User Management"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name","email","password","role"},
 *             @OA\Property(property="name", type="string", example="Karyawan Baru"),
 *             @OA\Property(property="email", type="string", example="karyawan@emanuelcorp.id"),
 *             @OA\Property(property="password", type="string", example="Password123!"),
 *             @OA\Property(property="role", type="string", enum={"owner","manajer","karyawan"}),
 *             @OA\Property(property="unit_bisnis_id", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(response=201, description="User berhasil dibuat"),
 *     @OA\Response(response=403, description="Akses ditolak")
 * )
 *
 * @OA\Put(
 *     path="/api/users/{id}",
 *     summary="Update user (owner only)",
 *     tags={"User Management"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string", example="Nama Baru"),
 *             @OA\Property(property="email", type="string", example="email@emanuelcorp.id"),
 *             @OA\Property(property="role", type="string", enum={"owner","manajer","karyawan"})
 *         )
 *     ),
 *     @OA\Response(response=200, description="User berhasil diupdate"),
 *     @OA\Response(response=403, description="Akses ditolak")
 * )
 *
 * @OA\Delete(
 *     path="/api/users/{id}",
 *     summary="Hapus user (owner only)",
 *     tags={"User Management"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="User berhasil dihapus"),
 *     @OA\Response(response=403, description="Akses ditolak")
 * )
 *
 * @OA\Patch(
 *     path="/api/users/{id}/toggle",
 *     summary="Toggle aktif/nonaktif user (owner only)",
 *     tags={"User Management"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Status user berhasil diubah"),
 *     @OA\Response(response=403, description="Akses ditolak")
 * )
 */
class UserAnnotation {}