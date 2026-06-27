<?php

namespace App\Swagger;

class KpiTemplateAnnotation
{
    /**
     * @OA\Get(
     * path="/api/kpi-templates",
     * summary="Get semua KPI template",
     * tags={"KPI Template"},
     * security={{"bearerAuth":{}}},
     * @OA\Response(response=200, description="List KPI template beserta relasi kpi_jenis & form_template")
     * )
     */
    public function index() {}

    /**
     * @OA\Get(
     * path="/api/kpi-templates/{id}",
     * summary="Get detail KPI template beserta form & fields",
     * tags={"KPI Template"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(response=200, description="Detail KPI template"),
     * @OA\Response(response=404, description="Tidak ditemukan")
     * )
     */
    public function show() {}

    /**
     * @OA\Get(
     * path="/api/kpi-templates/unit-bisnis/{unitBisnisId}",
     * summary="Get KPI template by unit bisnis (include relasi kpi_jenis & form_template)",
     * tags={"KPI Template"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="unitBisnisId", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(response=200, description="List KPI template by unit bisnis"),
     * @OA\Response(response=403, description="Akses ditolak - karyawan unit bisnis lain")
     * )
     */
    public function byUnitBisnis() {}

    /**
     * @OA\Post(
     * path="/api/kpi-templates",
     * summary="Buat KPI template baru dari katalog — otomatis buat form & fields (owner only)",
     * tags={"KPI Template"},
     * security={{"bearerAuth":{}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"unit_bisnis_id","kpi_jenis_id"},
     * @OA\Property(property="unit_bisnis_id", type="integer", example=1,
     * description="ID unit bisnis yang akan diberi KPI ini"),
     * @OA\Property(property="kpi_jenis_id", type="integer", example=1,
     * description="ID jenis KPI dari katalog (GET /kpi-jenis untuk daftar)"),
     * @OA\Property(property="nama", type="string", nullable=true,
     * description="Opsional — override nama tampilan",
     * example="Revenue Cluster de Matraman")
     * )
     * ),
     * @OA\Response(response=201, description="KPI template berhasil dibuat beserta form & fields otomatis"),
     * @OA\Response(response=422, description="Jenis KPI tidak aktif atau sudah ada di unit bisnis ini"),
     * @OA\Response(response=403, description="Akses ditolak")
     * )
     */
    public function store() {}

    /**
     * @OA\Put(
     * path="/api/kpi-templates/{id}",
     * summary="Update nama & deskripsi KPI template (owner only)",
     * tags={"KPI Template"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\RequestBody(
     * @OA\JsonContent(
     * @OA\Property(property="nama", type="string", example="Revenue Bulanan"),
     * @OA\Property(property="deskripsi", type="string", nullable=true)
     * )
     * ),
     * @OA\Response(response=200, description="KPI template berhasil diupdate"),
     * @OA\Response(response=403, description="Akses ditolak")
     * )
     */
    public function update() {}

    /**
     * @OA\Delete(
     * path="/api/kpi-templates/{id}",
     * summary="Hapus KPI template beserta form & periode terkait (owner only)",
     * tags={"KPI Template"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(response=200, description="KPI template beserta form dan periode terkait berhasil dihapus"),
     * @OA\Response(response=403, description="Akses ditolak")
     * )
     */
    public function destroy() {}
}