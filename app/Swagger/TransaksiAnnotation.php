<?php

namespace App\Swagger;

/**
 * @OA\Get(
 *     path="/api/transaksi",
 *     summary="Get semua transaksi",
 *     tags={"Transaksi"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(response=200, description="List transaksi")
 * )
 *
 * @OA\Get(
 *     path="/api/transaksi/{id}",
 *     summary="Get detail transaksi",
 *     tags={"Transaksi"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Detail transaksi"),
 *     @OA\Response(response=404, description="Tidak ditemukan")
 * )
 *
 * @OA\Get(
 *     path="/api/transaksi/unit-bisnis/{unitBisnisId}",
 *     summary="Get transaksi by unit bisnis & periode",
 *     tags={"Transaksi"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="unitBisnisId", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Parameter(name="bulan", in="query", required=true, @OA\Schema(type="integer", example=3)),
 *     @OA\Parameter(name="tahun", in="query", required=true, @OA\Schema(type="integer", example=2026)),
 *     @OA\Response(response=200, description="List transaksi by periode")
 * )
 *
 * @OA\Post(
 *     path="/api/transaksi",
 *     summary="Input transaksi baru",
 *     tags={"Transaksi"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"unit_bisnis_id","nama_customer","no_hp","tipe_transaksi","nominal"},
 *             @OA\Property(property="unit_bisnis_id", type="integer", example=3),
 *             @OA\Property(property="nama_customer", type="string", example="Michael"),
 *             @OA\Property(property="no_hp", type="string", example="081234567890"),
 *             @OA\Property(property="tipe_transaksi", type="string", enum={"booking_fee","dp","pelunasan","sewa_harian","penjualan","lainnya"}),
 *             @OA\Property(property="nominal", type="number", example=5000000),
 *             @OA\Property(property="omset", type="number", example=35000000),
 *             @OA\Property(property="tanggal_checkin", type="string", format="date", example="2026-03-28"),
 *             @OA\Property(property="tanggal_checkout", type="string", format="date", example="2026-03-30"),
 *             @OA\Property(property="keterangan", type="string", example="DP villa 2 malam"),
 *             @OA\Property(property="data_tambahan", type="object")
 *         )
 *     ),
 *     @OA\Response(response=201, description="Transaksi berhasil diinput"),
 *     @OA\Response(response=422, description="Duplikat transaksi")
 * )
 *
 * @OA\Put(
 *     path="/api/transaksi/{id}",
 *     summary="Update transaksi (owner only)",
 *     tags={"Transaksi"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Transaksi berhasil diupdate"),
 *     @OA\Response(response=403, description="Akses ditolak")
 * )
 *
 * @OA\Delete(
 *     path="/api/transaksi/{id}",
 *     summary="Hapus transaksi (owner only)",
 *     tags={"Transaksi"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     @OA\Response(response=200, description="Transaksi berhasil dihapus"),
 *     @OA\Response(response=403, description="Akses ditolak")
 * )
 */
class TransaksiAnnotation {}