<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UnitBisnisController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\KpiTemplateController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TransaksiController;
use App\Http\Controllers\Api\KpiPeriodController;
use App\Http\Controllers\Api\LaporanHarianController;
use App\Http\Controllers\Api\LaporanSopController;

// Public routes
Route::post('/login', [AuthController::class, 'login']);

// Protected routes  semua role
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Unit bisnis  semua role bisa lihat
    Route::get('/unit-bisnis', [UnitBisnisController::class, 'index']);
    Route::get('/unit-bisnis/{id}', [UnitBisnisController::class, 'show']);
    Route::get('/kategori-unit-bisnis', [UnitBisnisController::class, 'kategori']);

    // KPI Template - semua role bisa lihat
    Route::get('/kpi-templates', [KpiTemplateController::class, 'index']);
    Route::get('/kpi-templates/{id}', [KpiTemplateController::class, 'show']);
    Route::get('/kpi-templates/unit-bisnis/{unitBisnisId}', [KpiTemplateController::class, 'byUnitBisnis']);

     // KPI Period - semua role bisa lihat
    Route::get('/kpi-periods', [KpiPeriodController::class, 'index']);
    Route::get('/kpi-periods/unit-bisnis/{unitBisnisId}', [KpiPeriodController::class, 'byPeriode']);
    Route::get('/kpi-periods/summary', [KpiPeriodController::class, 'summary']);

    // Update realisasi - semua role
    Route::put('/kpi-periods/{id}/realisasi', [KpiPeriodController::class, 'updateRealisasi']);

    // Transaksi - semua role bisa lihat & input
    Route::get('/transaksi', [TransaksiController::class, 'index']);
    Route::get('/transaksi/{id}', [TransaksiController::class, 'show']);
    Route::get('/transaksi/unit-bisnis/{unitBisnisId}', [TransaksiController::class, 'byPeriode']);
    Route::post('/transaksi', [TransaksiController::class, 'store']);

    // Laporan Harian
    Route::get('/laporan-harian', [LaporanHarianController::class, 'index']);
    Route::get('/laporan-harian/{id}', [LaporanHarianController::class, 'show']);
    Route::get('/laporan-harian/unit-bisnis/{unitBisnisId}', [LaporanHarianController::class, 'byPeriode']);
    Route::post('/laporan-harian', [LaporanHarianController::class, 'store']);
    Route::put('/laporan-harian/{id}', [LaporanHarianController::class, 'update']);

    // Laporan SOP
    Route::get('/laporan-sop', [LaporanSopController::class, 'index']);
    Route::get('/laporan-sop/{id}', [LaporanSopController::class, 'show']);
    Route::post('/laporan-sop', [LaporanSopController::class, 'store']);

});

// Protected routes  owner only
Route::middleware(['auth:sanctum', 'isOwner'])->group(function () {
    // Unit bisnis CRUD
    Route::post('/unit-bisnis', [UnitBisnisController::class, 'store']);
    Route::put('/unit-bisnis/{id}', [UnitBisnisController::class, 'update']);
    Route::delete('/unit-bisnis/{id}', [UnitBisnisController::class, 'destroy']);

    // User management
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
    Route::patch('/users/{id}/toggle', [UserController::class, 'toggle']);

    // KPI Template CRUD
    Route::post('/kpi-templates', [KpiTemplateController::class, 'store']);
    Route::put('/kpi-templates/{id}', [KpiTemplateController::class, 'update']);
    Route::delete('/kpi-templates/{id}', [KpiTemplateController::class, 'destroy']);

        // KPI Period CRUD
    Route::post('/kpi-periods', [KpiPeriodController::class, 'store']);
    Route::put('/kpi-periods/{id}', [KpiPeriodController::class, 'update']);
    Route::delete('/kpi-periods/{id}', [KpiPeriodController::class, 'destroy']);

    // Transaksi - owner only bisa edit & hapus
    Route::put('/transaksi/{id}', [TransaksiController::class, 'update']);
    Route::delete('/transaksi/{id}', [TransaksiController::class, 'destroy']);

    // Laporan Harian - owner only bisa hapus
    Route::delete('/laporan-harian/{id}', [LaporanHarianController::class, 'destroy']);

    // Laporan SOP - owner only bisa review & hapus
    Route::patch('/laporan-sop/{id}/review', [LaporanSopController::class, 'review']);
    Route::delete('/laporan-sop/{id}', [LaporanSopController::class, 'destroy']);
});