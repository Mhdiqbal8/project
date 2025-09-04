<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ManagementUserController;
use App\Http\Controllers\ManagementInventarisController;
use App\Http\Controllers\ManagementObatController;
use App\Http\Controllers\RequestServiceController;
use App\Http\Controllers\BapController;
use App\Http\Controllers\KronologisFormController;
use App\Http\Controllers\PrivilegeController;
use App\Http\Controllers\LaporanKerjaController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\HR\DashboardController as HRDashboardController; // ✅ HR
use App\Http\Controllers\Hr\EmployeeProfileController as HREmployeeController;



/* === Redirect Dashboard === */
Route::get('/dashboard', fn () => redirect()->route('home'))->middleware('auth')->name('dashboard');
Route::get('/service-test', fn () => 'test masuk');

/* === Global Approve (legacy service) === */
Route::post('approve/service', [ServiceController::class, 'approve_form']);
Route::post('approve/urgent', [ServiceController::class, 'approveUrgent']);
Route::post('approve/modal/service', [ServiceController::class, 'approveModal'])->name('approve.modal.service');

Route::middleware('auth')->group(function () {

    /* ===== Dashboard ===== */
    Route::get('/', [HomeController::class, 'home'])->name('home');
    Route::post('ubah_password', [HomeController::class, 'ubah_password']);

    /* ===== Admin Panel ===== */
    Route::middleware('adminMiddleware')->group(function () {
        Route::get('management_user', [ManagementUserController::class, 'index'])->name('management_user.index');
        Route::post('management_user/store', [ManagementUserController::class, 'store']);
        Route::patch('management_user/update/{id}', [ManagementUserController::class, 'update'])->name('management_user.update');
        Route::get('management_user/edit/{id}', [ManagementUserController::class, 'edit'])->name('management_user.edit');
        Route::get('management_user/hapus-ttd/{id}', [ManagementUserController::class, 'hapusTTD'])->name('user.hapus_ttd');
        Route::delete('management_user/delete/{id}', [ManagementUserController::class, 'destroy'])->name('management_user.destroy');

        Route::get('management_inventaris', [ManagementInventarisController::class, 'index'])->name('management_inventaris');
        Route::post('management_inventaris/store', [ManagementInventarisController::class, 'store']);
        Route::patch('management_inventaris/update/{id}', [ManagementInventarisController::class, 'update']);

        Route::get('management_obat', [ManagementObatController::class, 'index'])->name('management_obat');
        Route::post('management_obat/store', [ManagementObatController::class, 'store']);
        Route::patch('management_obat/update/{id}', [ManagementObatController::class, 'update']);
    });

    /* ===== Akses Kelola Privileges (pakai Gate) ===== */
    Route::middleware('can:access_user_management')->group(function () {
        Route::get('privileges', [PrivilegeController::class, 'index'])->name('privileges.index');
        Route::get('privileges/{id}/edit', [PrivilegeController::class, 'edit'])->name('privileges.edit');
        Route::put('privileges/{id}', [PrivilegeController::class, 'update'])->name('privileges.update');
    });

    /* ===== Laporan Service (berdasarkan privilege) ===== */
    Route::middleware('can:laporan_service')->group(function () {
        Route::get('laporan_service', [LaporanController::class, 'laporan_service'])->name('laporan.service');
        Route::post('laporan_service/search', [LaporanController::class, 'search'])->name('laporan.service.search');
        Route::get('laporan_service/search_excel', [LaporanController::class, 'search_excel'])->name('laporan.service.search_excel');
        Route::get('laporan_service/search_pdf', [LaporanController::class, 'search_pdf'])->name('laporan.service.search_pdf');
        Route::get('laporan_service/search_pdf_single/{id}', [LaporanController::class, 'search_pdf_single'])->name('laporan.service.search_pdf_single');
    // (baru) laporan service BULANAN (buat file service_bulanan.blade.php)
  Route::get('laporan/service-bulanan', [LaporanController::class, 'serviceBulanan'])
    ->name('laporan.service.bulanan');

Route::get('laporan/service-bulanan/pdf', [LaporanController::class, 'serviceBulananPdf'])
    ->name('laporan.service.bulanan.pdf');


    // opsional: PDF bulanan (kalau sudah ada method-nya)
    // Route::get('laporan/service-bulanan/pdf', [LaporanController::class, 'serviceBulananPdf'])
    //     ->name('laporan.service.bulanan.pdf');
});

    /* ===== BAP ===== */
    Route::prefix('bap')->group(function () {
        Route::get('/', [BapController::class, 'index'])->name('bap.index');
        Route::get('form-bap', [BapController::class, 'formBap'])->name('bap.form_bap');
        Route::post('store-bap', [BapController::class, 'storeBap'])->name('bap.store_bap');
        Route::get('detail/bap/{id}', [BapController::class, 'detail'])->name('bap.detail');
        Route::get('cetak/{id}', [BapController::class, 'cetak'])->name('bap.cetak');

        // ⚠️ Pastikan ada method edit() di BapController. Kalau nggak ada, hapus baris ini.
        // Route::get('{id}/edit', [BapController::class, 'edit'])->name('bap.edit');

        // ✅ Tambahan: route yang diminta Blade (bap.kendala_update)
        Route::post('{id}/kendala-update', [BapController::class, 'updateKendala'])->name('bap.kendala_update');

        Route::patch('{id}', [BapController::class, 'update'])->name('bap.update');
        Route::delete('{id}', [BapController::class, 'destroy'])->name('bap.destroy');

        // ✅ approval flow versi baru:
        Route::post('{id}/approve', [BapController::class, 'approve'])->name('bap.approve');
        Route::post('{id}/approve-kepala-unit', [BapController::class, 'approveKepalaUnit'])->name('bap.approve_kepala_unit');
        Route::post('{id}/approve-supervision', [BapController::class, 'approveSupervision'])->name('bap.approve_supervision');
        Route::post('{id}/acc-mutu', [BapController::class, 'accMutu'])->name('bap.accMutu');

        // 🔖 tag unit & log
        Route::post('{id}/tag-unit', [BapController::class, 'tagUnit'])->name('bap.tagUnit');
        Route::get('{id}/tag-logs', [BapController::class, 'tagLogs'])->name('bap.tagLogs');

        // ❌ Legacy: route di bawah ini hapus kalau nggak ada method-nya di controller
        // Route::post('approve-it/{id}', [BapController::class, 'approveByIt'])->name('bap.approve_it');
        // Route::post('approve-manager/{id}', [BapController::class, 'approveByManager'])->name('bap.approve_manager');

        // ✅ Laporan BAP + PDF (dikunci privilege)
        Route::middleware('can:laporan_bap')->group(function () {
            Route::get('laporan/bap', [LaporanController::class, 'laporanBap'])->name('laporan.bap');
            Route::get('laporan/bap/pdf', [LaporanController::class, 'bapPdf'])->name('laporan.bap.pdf');
        });
    });

    /* ===== Kronologis ===== */
    Route::get('detail-kronologis/{id}', [KronologisFormController::class, 'detail'])->name('kronologis.view');
    Route::prefix('kronologis')->name('kronologis.')->group(function () {
        Route::get('{bapForm}/create', [KronologisFormController::class, 'create'])->name('create');
        Route::post('{bapForm}/store', [KronologisFormController::class, 'store'])->name('store');
        Route::get('{id}/edit', [KronologisFormController::class, 'edit'])->name('edit');
        Route::patch('{id}', [KronologisFormController::class, 'update'])->name('update');
        Route::delete('{id}', [KronologisFormController::class, 'destroy'])->name('destroy');
        Route::get('{id}/cetak', [KronologisFormController::class, 'cetak'])->name('cetak');
        Route::post('{id}/approve', [KronologisFormController::class, 'approve'])->name('approve');

        // ✅ Khusus Mutu: tandai "Sudah Dibaca Mutu"
        Route::post('{id}/mutu-check', [KronologisFormController::class, 'mutuCheck'])->name('mutuCheck');
    });

    /* ===== Service ===== */
    Route::get('service', [ServiceController::class, 'index'])->name('service.index');
    Route::post('service/store', [ServiceController::class, 'store'])->name('service.store');
    Route::get('service/{service}/edit', [ServiceController::class, 'edit'])->name('service.edit');
    Route::patch('service/{service}', [ServiceController::class, 'update'])->name('service.update');
    Route::delete('service/{service}', [ServiceController::class, 'destroy'])->name('service.destroy');
    Route::post('approve/service/{id}', [ServiceController::class, 'approve'])->name('service.approve');

    Route::get('approve_manager/service', [ServiceController::class, 'approve_manager']);
    Route::post('reject/service', [ServiceController::class, 'reject']);
    Route::post('get_inventaris/service', [ServiceController::class, 'getInventaris'])->name('get_inventaris_service');
    Route::get('check_data/service', [ServiceController::class, 'check_data_service']);
    Route::get('export/excel_service', [ServiceController::class, 'export_service_excel'])->name('service.export_excel');
    Route::get('service/{service}', [ServiceController::class, 'show'])->name('service.show');
    Route::get('get-inventaris/{id}', [ServiceController::class, 'getInventarisByJenis']);
    Route::get('/count-badge', [ServiceController::class, 'countBadge'])->name('count.badge');

    /* ===== Request Service ===== */
    Route::prefix('request_service')->name('request_service.')->group(function () {
        Route::get('/', [RequestServiceController::class, 'index_request'])->name('index');
        Route::get('{id}', [RequestServiceController::class, 'show'])->name('show');
        Route::get('approve/{id}', [RequestServiceController::class, 'approve'])->name('approve');
        Route::patch('{id}/approve-progress', [RequestServiceController::class, 'approveProgress'])->name('approveProgress');
        Route::patch('{id}/approve-finish', [RequestServiceController::class, 'approveFinish'])->name('approveFinish');
        Route::post('reject', [RequestServiceController::class, 'reject'])->name('reject');
        Route::post('onprogress/{id}', [RequestServiceController::class, 'onprogress'])->name('onprogress');
        Route::post('selesai/{id}', [RequestServiceController::class, 'selesai'])->name('selesai');
        Route::post('closed/{id}', [RequestServiceController::class, 'closed'])->name('closed');
        Route::get('export', [RequestServiceController::class, 'exportExcel'])->name('export');
    });

    /* ===== Laporan Kerja ===== */
    Route::prefix('laporan-kerja')->name('laporan-kerja.')->group(function () {
        Route::get('/', [LaporanKerjaController::class, 'index'])->name('index');
        Route::get('/create', [LaporanKerjaController::class, 'create'])->name('create');
        Route::post('/store', [LaporanKerjaController::class, 'store'])->name('store');
        Route::get('/{id}', [LaporanKerjaController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [LaporanKerjaController::class, 'edit'])->name('edit');
        Route::patch('/{id}', [LaporanKerjaController::class, 'update'])->name('update');
        Route::get('/{id}/cetak', [LaporanKerjaController::class, 'cetak'])->name('cetak');
        Route::post('/{id}/approve', [LaporanKerjaController::class, 'approve'])->name('approve');
        Route::post('/{id}/komentar', [LaporanKerjaController::class, 'simpanKomentar'])->name('komentar');
        Route::patch('komentar/{komentar}/beres', [LaporanKerjaController::class, 'beresKomentar'])->name('komentar.beres');
        Route::get('/export-excel', [LaporanKerjaController::class, 'exportExcel'])->name('export-excel');
    });

    /* ===== Activity Logs ===== */
    Route::middleware('can:view-activity-logs')->group(function () {
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity.index');
    });

    /* ===== Notifikasi ===== */
    Route::get('/notif/read/{id}', [NotificationController::class, 'read'])->name('notif.read');
    Route::post('/notif/read-all', [NotificationController::class, 'readAll'])->name('notif.read_all');
    Route::get('/notif/unread', [NotificationController::class, 'unread'])->name('notif.unread');
    Route::get('/notif/all', [NotificationController::class, 'all'])->name('notif.all');
    Route::get('/notif/go/{id}', [NotificationController::class, 'go'])->name('notif.go');
});

/* ===== E-Personalia (HR) ===== */
Route::middleware(['auth','can:access_personalia'])
    ->prefix('hr')->as('hr.')
    ->group(function () {
        // Dashboard HR
        Route::get('/', [HRDashboardController::class, 'index'])->name('dashboard');

        // Master Karyawan
        Route::prefix('employees')->name('employees.')->group(function () {
            // GET cukup access_personalia (sudah dijaga di group & constructor controller)
            Route::get('/', [HREmployeeController::class, 'index'])->name('index');

            // POST wajib hr_employee_manage
            Route::post('/', [HREmployeeController::class, 'store'])
                 ->middleware('can:hr_employee_manage')
                 ->name('store');
        });
    });




require __DIR__ . '/auth.php';
