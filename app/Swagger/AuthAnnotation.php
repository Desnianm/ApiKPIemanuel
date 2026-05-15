<?php

namespace App\Swagger;

/**
 * @OA\Post(
 * path="/api/login",
 * summary="Login user",
 * tags={"Auth"},
 * @OA\RequestBody(
 * required=true,
 * @OA\JsonContent(
 * required={"email","password"},
 * @OA\Property(property="email", type="string", example="agung@emanuelcorp.id"),
 * @OA\Property(property="password", type="string", example="AgungCorp2024!")
 * )
 * ),
 * @OA\Response(
 * response=200, 
 * description="Login berhasil",
 * @OA\JsonContent(
 * @OA\Property(property="message", type="string", example="Login berhasil."),
 * @OA\Property(property="token", type="string", example="1|token_string"),
 * @OA\Property(property="user", type="object",
 * @OA\Property(property="id", type="integer", example=1),
 * @OA\Property(property="name", type="string", example="Little Bali Villa"),
 * @OA\Property(property="email", type="string", example="lbv@emanuelcorp.id"),
 * @OA\Property(property="role", type="string", example="karyawan"),
 * @OA\Property(property="unit_bisnis_id", type="integer", example=3),
 * @OA\Property(property="photo", type="string", example="photos/default.jpeg"),
 * @OA\Property(property="photo_url", type="string", example="http://103.253.213.207/storage/photos/default.jpeg")
 * )
 * )
 * ),
 * @OA\Response(response=422, description="Validasi gagal"),
 * @OA\Response(response=403, description="Akun tidak aktif")
 * )
 *
 * @OA\Get(
 * path="/api/me",
 * summary="Get profile user yang sedang login",
 * tags={"Auth"},
 * security={{"bearerAuth":{}}},
 * @OA\Response(
 * response=200, 
 * description="Data user",
 * @OA\JsonContent(
 * @OA\Property(property="user", type="object",
 * @OA\Property(property="id", type="integer", example=1),
 * @OA\Property(property="name", type="string", example="Little Bali Villa"),
 * @OA\Property(property="photo_url", type="string", example="http://103.253.213.207/storage/photos/default.jpeg")
 * )
 * )
 * ),
 * @OA\Response(response=401, description="Unauthenticated")
 * )
 */
class AuthAnnotation {}