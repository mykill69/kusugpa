<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginAuthController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\UpdatesController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\LoanReportController;
use App\Http\Controllers\LoanAttachmentController; 

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Guest Routes
Route::middleware(['guest'])->group(function () {
    Route::get('/', function () {
        return view('login.login');
    });
    
    Route::get('/login', [LoginAuthController::class, 'getLogin'])->name('getLogin');
    Route::post('/login', [LoginAuthController::class, 'postLogin'])->name('postLogin');
});

// Admin Login Routes
Route::prefix('system/auth')->name('admin.')->group(function () {
    Route::get('/admin-login', [LoginAuthController::class, 'getAdminLogin'])->name('getLogin');
    Route::post('/admin-login', [LoginAuthController::class, 'postAdminLogin'])->name('postLogin');
});

// Authenticated Routes
Route::middleware(['login_auth'])->group(function () {
    // Logout
    Route::get('/logout', [LoginAuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [MenuController::class, 'dashboard'])->name('dashboard');

    // ==================== LOAN ROUTES (ALL STATIC BEFORE WILDCARD) ====================
    Route::get('/loans', [LoanController::class, 'index'])->name('loans.index');

    // Loan Attachments
    Route::get('/loans/{loan}/attachments', [LoanAttachmentController::class, 'index'])->name('loans.attachments.index');
    Route::post('/loans/{loan}/attachments', [LoanAttachmentController::class, 'store'])->name('loans.attachments.store');
    Route::get('/loans/attachments/{attachment}/view', [LoanAttachmentController::class, 'view'])->name('loans.attachment.view');
    Route::get('/loans/attachments/{attachment}/download', [LoanAttachmentController::class, 'download'])->name('loans.attachment.download');
    Route::delete('/loans/attachments/{attachment}', [LoanAttachmentController::class, 'destroy'])->name('loans.attachments.destroy');
    
    // Loan Reports (static)
    Route::get('/loans/reports', [LoanReportController::class, 'index'])->name('loans.reports');
    Route::get('/loans/monthly-report-pdf', [LoanReportController::class, 'monthlyReportPDF'])->name('loans.monthly-report-pdf');
    Route::get('/loans/active-loans-pdf', [LoanReportController::class, 'activeLoansPDF'])->name('loans.active-loans-pdf');
    
    // Loan Settings (static)
    Route::get('/loans/settings', [LoanController::class, 'settings'])->name('loans.settings');
    Route::post('/loans/settings', [LoanController::class, 'updateSettings'])->name('loans.settings.update');
    Route::post('/loans/types', [LoanController::class, 'storeLoanType'])->name('loans.types.store');
    Route::post('/loans/types/{loanType}/toggle', [LoanController::class, 'toggleLoanType'])->name('loans.types.toggle');
    
    // Loan Create (static)
    Route::get('/loans/create', [LoanController::class, 'create'])->name('loans.create');
    Route::post('/loans', [LoanController::class, 'store'])->name('loans.store');
    
    // Individual Loan PDF Reports (with {loan} parameter)
    Route::get('/loans/{loan}/amortization-pdf', [LoanReportController::class, 'amortizationPDF'])->name('loans.amortization-pdf');
    Route::get('/loans/{loan}/details-pdf', [LoanReportController::class, 'detailsPDF'])->name('loans.details-pdf');
    Route::get('/loans/{loan}/soa-pdf', [LoanReportController::class, 'soaPDF'])->name('loans.soa-pdf');
    
    // Individual Loan Actions
    Route::post('/loans/{loan}/approve', [LoanController::class, 'approve'])->name('loans.approve');
    Route::post('/loans/{loan}/activate', [LoanController::class, 'activate'])->name('loans.activate');
    Route::post('/loans/{loan}/reject', [LoanController::class, 'reject'])->name('loans.reject');
    Route::post('/loans/{loan}/payment', [LoanController::class, 'recordPayment'])->name('loans.payment');
    Route::delete('/loans/{loan}', [LoanController::class, 'destroy'])->name('loans.destroy');
    
    // Loan Show - MUST BE VERY LAST
    Route::get('/loans/{loan}', [LoanController::class, 'show'])->name('loans.show');
    // ==================== END LOAN ROUTES ====================

    // Settings Routes
    Route::post('/updates/add-crop-year', [UpdatesController::class, 'addCropYear'])
        ->name('updates.addCropYear');
    Route::post('/updates/add-week-number', [UpdatesController::class, 'addWeekNumber'])
        ->name('updates.addWeekNumber');
    Route::post('/updates/add-quedan-price', [UpdatesController::class, 'addQuedanPrice'])
        ->name('updates.addQuedanPrice');
    Route::post('/updates/add-molasses-price', [UpdatesController::class, 'addMolassesPrice'])
        ->name('updates.addMolassesPrice');

    // Upload Routes
    Route::post('/upload/{type}', [UploadController::class, 'uploadCSV'])
    ->where('type', 'summary|trucking|fuel|rentals|underload|transloading|fci|mudpress')
    ->name('upload.csv');

    // Dashboard API endpoints
    Route::get('/dashboard/data', [MenuController::class, 'dashboardData'])->name('dashboard.data');
    Route::get('/dashboard/weekly', [MenuController::class, 'dashboardWeekly'])->name('dashboard.weekly');

    // Reports
    Route::get('/summary-report', [MenuController::class, 'summaryReport'])->name('summaryReport');
    Route::get('/summary/pdf-preview', [MenuController::class, 'previewPDF'])->name('summary.previewPDF');
    Route::get('/summary/download-pdf', [MenuController::class, 'downloadPDF'])->name('summary.downloadPDF');

    // Print Voucher
    Route::get('/print-voucher', [MenuController::class, 'printVoucher'])->name('printVoucher');
    Route::get('/voucher/pdf-preview', [MenuController::class, 'voucherPDF'])->name('voucher.pdf');

    // User Management
    Route::get('/user-management', [UserManagementController::class, 'userManagement'])
        ->name('user-management');
    Route::post('/user-management', [UserManagementController::class, 'store'])
        ->name('user-management.store');
    Route::put('/user-management/{user}', [UserManagementController::class, 'update'])
        ->name('user-management.update');
    Route::delete('/user-management/{user}', [UserManagementController::class, 'destroy'])
        ->name('user-management.destroy');
    Route::post('/user-management/assign-permissions', [UserManagementController::class, 'assignPermissions'])
        ->name('assign-permissions');
    Route::get('/permissions/list', [UserManagementController::class, 'permissionsList'])->name('permissions.list');

    // Admin Routes (require admin role middleware)
    Route::middleware(['admin.role'])->prefix('system')->name('admin.')->group(function () {
        Route::get('/panel', [MenuController::class, 'adminDashboard'])->name('dashboard');
        Route::get('/settings', [MenuController::class, 'adminSettings'])->name('settings');
        Route::get('/settings/data', [MenuController::class, 'getSettingsData'])->name('settings.data');
        Route::post('/settings/update', [MenuController::class, 'updateSetting'])->name('settings.update');
        
        Route::post('/cache/clear', [MenuController::class, 'clearCache'])->name('cache.clear');
        Route::post('/backup/create', [MenuController::class, 'createBackup'])->name('backup.create');
    });
});