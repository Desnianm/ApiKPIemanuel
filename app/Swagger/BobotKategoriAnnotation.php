<?php

namespace App\Swagger;

class BobotKategoriAnnotation
{
    /**
     * @OA\Get(
     * path="/api/bobot-kategori",
     * summary="Get daftar bobot kategori untuk periode tertentu (owner only)",
     * tags={"Bobot Kategori"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="periode", in="query", required=true, description="Tahun periode", @OA\Schema(type="integer", example=2026)),
     * @OA\Response(
     * response=200,
     * description="Daftar bobot per kategori, termasuk kategori yang belum punya bobot (default 0)",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="boolean", example=true),
     * @OA\Property(property="data", type="array",
     * @OA\Items(
     * @OA\Property(property="kategori_id", type="integer", example=1),
     * @OA\Property(property="nama_kategori", type="string", example="Properti"),
     * @OA\Property(property="periode", type="integer", example=2026),
     * @OA\Property(property="bobot_persen", type="number", example=40),
     * @OA\Property(property="ditetapkan_oleh", type="integer", example=1, nullable=true),
     * @OA\Property(property="tanggal_ditetapkan", type="string", example="2026-08-11T07:23:58.000000Z", nullable=true)
     * )
     * )
     * )
     * ),
     * @OA\Response(response=401, description="Unauthenticated"),
     * @OA\Response(response=403, description="Akses ditolak"),
     * @OA\Response(response=422, description="Validasi gagal - periode wajib diisi")
     * )
     */
    public function index() {}

    /**
     * @OA\Post(
     * path="/api/bobot-kategori",
     * summary="Simpan/update bobot kategori untuk 1 periode sekaligus, total harus 100% (owner only)",
     * tags={"Bobot Kategori"},
     * security={{"bearerAuth":{}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"periode","bobot"},
     * @OA\Property(property="periode", type="integer", example=2026),
     * @OA\Property(property="bobot", type="array",
     * @OA\Items(
     * @OA\Property(property="kategori_id", type="integer", example=1),
     * @OA\Property(property="bobot_persen", type="number", example=40)
     * )
     * )
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Bobot kategori berhasil disimpan",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="boolean", example=true),
     * @OA\Property(property="message", type="string", example="Bobot kategori berhasil disimpan")
     * )
     * ),
     * @OA\Response(response=401, description="Unauthenticated"),
     * @OA\Response(response=403, description="Akses ditolak"),
     * @OA\Response(
     * response=422,
     * description="Validasi gagal - total bobot semua kategori bukan 100%",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="boolean", example=false),
     * @OA\Property(property="message", type="string", example="Total bobot semua kategori harus 100%. Total saat ini: 70%")
     * )
     * )
     * )
     */
    public function store() {}
}