<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\Partner;
use App\Http\Controllers\VerificationController;
use Illuminate\Support\Facades\Route;

// Public verification
Route::get('/verify/rider/{cardNumber}', [VerificationController::class, 'verifyRider'])
    ->name('verify.rider');

// Landing
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('partner.dashboard');
    }
    return view('welcome');
});

// Auth routes
require __DIR__.'/auth.php';

// Onboarding
Route::middleware('auth')->group(function () {
    Route::get('/onboarding', [OnboardingController::class, 'show'])->name('onboarding');
    Route::post('/onboarding', [OnboardingController::class, 'store'])->name('onboarding.store');
});

// Partner Portal
Route::middleware(['auth'])->prefix('portal')->name('partner.')->group(function () {
    Route::get('/dashboard', [Partner\DashboardController::class, 'index'])->name('dashboard');

    // Fleet Management
    Route::resource('fleet', Partner\FleetController::class);

    // Rider Management
    Route::resource('riders', Partner\RiderController::class);
    Route::post('/riders/{rider}/generate-id-card', [Partner\RiderController::class, 'generateIdCard'])->name('riders.generate-id-card');
    Route::post('/riders/{rider}/suspend', [Partner\RiderController::class, 'suspend'])->name('riders.suspend');
    Route::post('/riders/{rider}/activate', [Partner\RiderController::class, 'activate'])->name('riders.activate');

    // Earnings
    Route::get('/earnings', [Partner\EarningsController::class, 'index'])->name('earnings.index');

    // Maintenance
    Route::resource('maintenance', Partner\MaintenanceController::class)->only(['index', 'create', 'store', 'show']);

    // Wallet
    Route::get('/wallet', [Partner\WalletController::class, 'index'])->name('wallet.index');
    Route::post('/wallet/withdraw', [Partner\WalletController::class, 'requestWithdrawal'])->name('wallet.withdraw');

    // Compliance
    Route::get('/compliance', [Partner\ComplianceController::class, 'index'])->name('compliance.index');

    // Documents
    Route::get('/documents', [Partner\DocumentController::class, 'index'])->name('documents.index');
    Route::post('/documents', [Partner\DocumentController::class, 'store'])->name('documents.store');
    Route::delete('/documents/{document}', [Partner\DocumentController::class, 'destroy'])->name('documents.destroy');

    // Reports
    Route::get('/reports', [Partner\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/fleet', [Partner\ReportController::class, 'fleet'])->name('reports.fleet');
    Route::get('/reports/earnings', [Partner\ReportController::class, 'earnings'])->name('reports.earnings');

    // Alerts
    Route::get('/alerts', [Partner\AlertController::class, 'index'])->name('alerts.index');
    Route::post('/alerts/{alert}/read', [Partner\AlertController::class, 'markRead'])->name('alerts.read');
});

// Admin Portal
Route::middleware(['auth', 'role:super_admin|finance_admin|compliance_admin|operations_admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

        // Partners
        Route::get('/partners', [Admin\PartnerController::class, 'index'])->name('partners.index');
        Route::get('/partners/{partner}', [Admin\PartnerController::class, 'show'])->name('partners.show');
        Route::post('/partners/{partner}/approve', [Admin\PartnerController::class, 'approve'])->name('partners.approve');
        Route::post('/partners/{partner}/suspend', [Admin\PartnerController::class, 'suspend'])->name('partners.suspend');
        Route::post('/partners/{partner}/earnings', [Admin\PartnerController::class, 'uploadEarnings'])->name('partners.upload-earnings');

        // Withdrawals
        Route::get('/withdrawals', [Admin\WithdrawalController::class, 'index'])->name('withdrawals.index');
        Route::post('/withdrawals/{withdrawal}/approve', [Admin\WithdrawalController::class, 'approve'])->name('withdrawals.approve');
        Route::post('/withdrawals/{withdrawal}/complete', [Admin\WithdrawalController::class, 'complete'])->name('withdrawals.complete');
        Route::post('/withdrawals/{withdrawal}/reject', [Admin\WithdrawalController::class, 'reject'])->name('withdrawals.reject');

        // Fleet overview
        Route::get('/fleet', [Admin\FleetOverviewController::class, 'index'])->name('fleet.index');

        // Reports
        Route::get('/reports', [Admin\ReportController::class, 'index'])->name('reports.index');
    });
