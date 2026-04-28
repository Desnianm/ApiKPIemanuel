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
use App\Http\Controllers\Api\PayrollReminderController;
use App\Http\Controllers\Api\SopController;
use App\Http\Controllers\Api\AuditLogController;    
use App\Http\Controllers\Api\FormTemplateController;
use App\Http\Controllers\Api\FormSubmissionController;
use App\Http\Controllers\Api\LeaderboardController;

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

    // SOP - semua role bisa lihat
    Route::get('/sop', [SopController::class, 'index']);
    Route::get('/sop/{id}', [SopController::class, 'show']);

    // Form Template - semua role bisa lihat
    Route::get('/form-templates', [FormTemplateController::class, 'index']);
    Route::get('/form-templates/{id}', [FormTemplateController::class, 'show']);
    Route::get('/form-templates/unit-bisnis/{unitBisnisId}', [FormTemplateController::class, 'byUnitBisnis']);

    // Form Submission - semua role bisa lihat & submit
    Route::get('/form-submissions', [FormSubmissionController::class, 'index']);
    Route::get('/form-submissions/{id}', [FormSubmissionController::class, 'show']);
    Route::get('/form-submissions/form/{formTemplateId}', [FormSubmissionController::class, 'byForm']);
    Route::post('/form-submissions', [FormSubmissionController::class, 'store']);

    //leaderboard
    Route::get('/leaderboard', [LeaderboardController::class, 'index']);

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

    // Payroll Reminder
    Route::get('/payroll-reminders', [PayrollReminderController::class, 'index']);
    Route::get('/payroll-reminders/user/{userId}', [PayrollReminderController::class, 'byUser']);
    Route::post('/payroll-reminders', [PayrollReminderController::class, 'store']);
    Route::put('/payroll-reminders/{id}', [PayrollReminderController::class, 'update']);
    Route::patch('/payroll-reminders/{id}/toggle', [PayrollReminderController::class, 'toggleReminder']);
    Route::delete('/payroll-reminders/{id}', [PayrollReminderController::class, 'destroy']);

    // SOP CRUD
    Route::post('/sop', [SopController::class, 'store']);
    Route::put('/sop/{id}', [SopController::class, 'update']);
    Route::delete('/sop/{id}', [SopController::class, 'destroy']);

    // Audit Log
    Route::get('/audit-logs', [AuditLogController::class, 'index']);
    Route::get('/audit-logs/user/{userId}', [AuditLogController::class, 'byUser']);

    // Form Template & Submission - owner only bisa CRUD
    Route::post('/form-templates', [FormTemplateController::class, 'store']);
    Route::put('/form-templates/{id}', [FormTemplateController::class, 'update']);
    Route::delete('/form-templates/{id}', [FormTemplateController::class, 'destroy']);
    Route::patch('/form-templates/{id}/toggle', [FormTemplateController::class, 'toggle']);

    // Owner bisa hapus submission
    Route::delete('/form-submissions/{id}', [FormSubmissionController::class, 'destroy']);
});