<?php

namespace App\Swagger;

/**
 * @OA\Get(
 *     path="/api/form-templates",
 *     summary="Get semua form template",
 *     tags={"Form Builder"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(response=200, description="List form template")
 * )
 *
 * @OA\Get(
 *     path="/api/form-templates/{id}",
 *     summary="Get detail form template",
 *     tags={"Form Builder"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Detail form template"),
 *     @OA\Response(response=404, description="Form template tidak ditemukan")
 * )
 *
 * @OA\Get(
 *     path="/api/form-templates/unit-bisnis/{unitBisnisId}",
 *     summary="Get form template berdasarkan unit bisnis",
 *     tags={"Form Builder"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="unitBisnisId", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="List form template by unit bisnis")
 * )
 *
 * @OA\Post(
 *     path="/api/form-templates",
 *     summary="Buat form template baru (owner only)",
 *     tags={"Form Builder"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"unit_bisnis_id","nama","fields"},
 *             @OA\Property(property="unit_bisnis_id", type="integer", example=1),
 *             @OA\Property(property="nama", type="string", example="Form Pendapatan Harian"),
 *             @OA\Property(property="deskripsi", type="string", example="Form input pendapatan harian"),
 *             @OA\Property(property="is_active", type="boolean", example=true),
 *             @OA\Property(
 *                 property="fields",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="label", type="string", example="Total Pendapatan"),
 *                     @OA\Property(property="tipe", type="string", enum={"text","number","date","select","textarea"}, example="number"),
 *                     @OA\Property(property="wajib", type="boolean", example=true),
 *                     @OA\Property(property="urutan", type="integer", example=1),
 *                     @OA\Property(property="kpi_template_id", type="integer", example=2, nullable=true)
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(response=201, description="Form template berhasil dibuat"),
 *     @OA\Response(response=422, description="Validasi gagal"),
 *     @OA\Response(response=403, description="Akses ditolak")
 * )
 *
 * @OA\Put(
 *     path="/api/form-templates/{id}",
 *     summary="Update form template (owner only)",
 *     tags={"Form Builder"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="nama", type="string", example="Form Pendapatan Harian Update"),
 *             @OA\Property(property="deskripsi", type="string", example="Deskripsi baru"),
 *             @OA\Property(property="is_active", type="boolean", example=true),
 *             @OA\Property(
 *                 property="fields",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="label", type="string", example="Total Pendapatan"),
 *                     @OA\Property(property="tipe", type="string", enum={"text","number","date","select","textarea"}, example="number"),
 *                     @OA\Property(property="wajib", type="boolean", example=true),
 *                     @OA\Property(property="urutan", type="integer", example=1),
 *                     @OA\Property(property="kpi_template_id", type="integer", example=2, nullable=true)
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(response=200, description="Form template berhasil diupdate"),
 *     @OA\Response(response=404, description="Form template tidak ditemukan"),
 *     @OA\Response(response=403, description="Akses ditolak")
 * )
 *
 * @OA\Patch(
 *     path="/api/form-templates/{id}/toggle",
 *     summary="Aktifkan/nonaktifkan form template (owner only)",
 *     tags={"Form Builder"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Status form template berhasil diubah"),
 *     @OA\Response(response=404, description="Form template tidak ditemukan"),
 *     @OA\Response(response=403, description="Akses ditolak")
 * )
 *
 * @OA\Delete(
 *     path="/api/form-templates/{id}",
 *     summary="Hapus form template (owner only)",
 *     tags={"Form Builder"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Form template berhasil dihapus"),
 *     @OA\Response(response=404, description="Form template tidak ditemukan"),
 *     @OA\Response(response=403, description="Akses ditolak")
 * )
 *
 * @OA\Get(
 *     path="/api/form-submissions",
 *     summary="Get semua submission form",
 *     tags={"Form Builder"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(response=200, description="List submission form")
 * )
 *
 * @OA\Get(
 *     path="/api/form-submissions/{id}",
 *     summary="Get detail submission form",
 *     tags={"Form Builder"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Detail submission form"),
 *     @OA\Response(response=404, description="Submission tidak ditemukan")
 * )
 *
 * @OA\Get(
 *     path="/api/form-submissions/form/{formTemplateId}",
 *     summary="Get submission berdasarkan form template",
 *     tags={"Form Builder"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="formTemplateId", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="List submission by form template")
 * )
 *
 * @OA\Post(
 *     path="/api/form-submissions",
 *     summary="Submit form (karyawan)",
 *     tags={"Form Builder"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"form_template_id","answers"},
 *             @OA\Property(property="form_template_id", type="integer", example=2),
 *             @OA\Property(
 *                 property="answers",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="form_field_id", type="integer", example=4),
 *                     @OA\Property(property="nilai", type="string", example="5")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(response=201, description="Form berhasil disubmit"),
 *     @OA\Response(response=422, description="Field wajib belum diisi"),
 *     @OA\Response(response=403, description="Tidak bisa submit form unit bisnis lain")
 * )
 *
 * @OA\Delete(
 *     path="/api/form-submissions/{id}",
 *     summary="Hapus submission (owner only)",
 *     tags={"Form Builder"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Submission berhasil dihapus"),
 *     @OA\Response(response=404, description="Submission tidak ditemukan"),
 *     @OA\Response(response=403, description="Akses ditolak")
 * )
 */
class FormAnnotation {}