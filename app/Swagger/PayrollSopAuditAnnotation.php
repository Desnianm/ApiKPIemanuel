<?php

namespace App\Swagger;

/**
 * @OA\Get(
 *     path="/api/payroll-reminders",
 *     summary="Get semua payroll reminder (owner only)",
 *     tags={"Payroll & SOP"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(response=200, description="List payroll reminder")
 * )
 *
 * @OA\Post(
 *     path="/api/payroll-reminders",
 *     summary="Buat payroll reminder (owner only)",
 *     tags={"Payroll & SOP"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"user_id","periode_bulan","periode_tahun","gaji_pokok"},
 *             @OA\Property(property="user_id", type="integer", example=1),
 *             @OA\Property(property="periode_bulan", type="integer", example=3),
 *             @OA\Property(property="periode_tahun", type="integer", example=2026),
 *             @OA\Property(property="gaji_pokok", type="number", example=3000000),
 *             @OA\Property(property="tunjangan", type="number", example=500000),
 *             @OA\Property(property="potongan", type="number", example=100000),
 *             @OA\Property(property="catatan", type="string", example="Gaji bulan Maret")
 *         )
 *     ),
 *     @OA\Response(response=201, description="Payroll reminder berhasil dibuat"),
 *     @OA\Response(response=403, description="Akses ditolak")
 * )
 *
 * @OA\Patch(
 *     path="/api/payroll-reminders/{id}/toggle",
 *     summary="Toggle sudah diingatkan (owner only)",
 *     tags={"Payroll & SOP"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Status reminder berhasil diubah"),
 *     @OA\Response(response=403, description="Akses ditolak")
 * )
 *
 * @OA\Get(
 *     path="/api/sop",
 *     summary="Get semua SOP",
 *     tags={"Payroll & SOP"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(response=200, description="List SOP")
 * )
 *
 * @OA\Post(
 *     path="/api/sop",
 *     summary="Buat SOP baru (owner only)",
 *     tags={"Payroll & SOP"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"judul","konten"},
 *             @OA\Property(property="unit_bisnis_id", type="integer", example=1),
 *             @OA\Property(property="judul", type="string", example="Aturan Input Transaksi"),
 *             @OA\Property(property="konten", type="string", example="Setiap transaksi wajib disertai bukti bayar"),
 *             @OA\Property(property="urutan", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(response=201, description="SOP berhasil dibuat"),
 *     @OA\Response(response=403, description="Akses ditolak")
 * )
 *
 * @OA\Get(
 *     path="/api/audit-logs",
 *     summary="Get semua audit log (owner only)",
 *     tags={"Audit Log"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(response=200, description="List audit log"),
 *     @OA\Response(response=403, description="Akses ditolak")
 * )
 *
 * @OA\Get(
 *     path="/api/audit-logs/user/{userId}",
 *     summary="Get audit log by user (owner only)",
 *     tags={"Audit Log"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="List audit log by user"),
 *     @OA\Response(response=403, description="Akses ditolak")
 * )
 */
class PayrollSopAuditAnnotation {}