<?php

namespace App\Swagger;

/**
 * @OA\Post(
 *     path="/api/login",
 *     summary="Login user",
 *     tags={"Auth"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email","password"},
 *             @OA\Property(property="email", type="string", example="agung@emanuelcorp.id"),
 *             @OA\Property(property="password", type="string", example="AgungCorp2024!")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Login berhasil"),
 *     @OA\Response(response=422, description="Validasi gagal"),
 *     @OA\Response(response=403, description="Akun tidak aktif")
 * )
 *
 * @OA\Post(
 *     path="/api/logout",
 *     summary="Logout user",
 *     tags={"Auth"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(response=200, description="Logout berhasil"),
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 *
 * @OA\Get(
 *     path="/api/me",
 *     summary="Get profile user yang sedang login",
 *     tags={"Auth"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(response=200, description="Data user"),
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 */
class AuthAnnotation {}