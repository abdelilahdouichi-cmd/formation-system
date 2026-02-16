<?php

use App\Enums\UserRole;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\TwoFactorAuthenticationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('two-factor-authentication', [TwoFactorAuthenticationController::class, 'show'])->name('two-factor.show');
    Route::post('two-factor-authentication', [TwoFactorAuthenticationController::class, 'store'])->name('two-factor.store');
    Route::post('two-factor-authentication/confirm', [TwoFactorAuthenticationController::class, 'confirm'])->name('two-factor.confirm');
    Route::delete('two-factor-authentication', [TwoFactorAuthenticationController::class, 'disable'])->name('two-factor.disable');

    Route::get('verify-email', EmailVerificationPromptController::class)->name('verification.notice');
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'send'])->name('verification.send');
    Route::get('verify-email/{id}/{hash}', function (Request $request) {
        if (! hash_equals((string) $request->route('hash'), sha1($request->user()->getEmailForVerification()))) {
            abort(403);
        }

        if (! $request->user()->hasVerifiedEmail()) {
            $request->user()->markEmailAsVerified();

            event(new \Illuminate\Auth\Events\Verified($request->user()));
        }

        return redirect()->route('dashboard')->with('status', 'Email vérifié avec succès!');
    })->middleware('throttle:6,1')->name('verification.verify');

    Route::get('dashboard', function () {
        return view('dashboard');
    })->middleware('verified')->name('dashboard');
});

Route::middleware(['auth', 'role:' . UserRole::adminRoles(), 'log.admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('users', UserController::class)->except('show');

        // Formation routes
        Route::resource('formations', \App\Http\Controllers\Formation\FormationController::class);
        Route::post('formations/{formation}/execute', [\App\Http\Controllers\Formation\FormationController::class, 'execute'])->name('formations.execute');
        Route::post('formations/{formation}/cancel', [\App\Http\Controllers\Formation\FormationController::class, 'cancel'])->name('formations.cancel');
        Route::post('formations/{formation}/archive', [\App\Http\Controllers\Formation\FormationController::class, 'archive'])->name('formations.archive');

        // Classes routes
        Route::resource('formations.classes', \App\Http\Controllers\Formation\ClasseController::class);

        // Participants routes
        Route::resource('formations.participants', \App\Http\Controllers\Formation\ParticipantController::class)
            ->names('participants');
        Route::post('formations/{formation}/participants/{participant}/assign-classe', [\App\Http\Controllers\Formation\ParticipantController::class, 'assignToClasse'])->name('participants.assign-classe');
        Route::post('formations/{formation}/participants/{participant}/deactivate', [\App\Http\Controllers\Formation\ParticipantController::class, 'deactivate'])->name('participants.deactivate');
        Route::post('formations/{formation}/participants/{participant}/activate', [\App\Http\Controllers\Formation\ParticipantController::class, 'activate'])->name('participants.activate');

        // Qualifications routes
        Route::resource('formations.qualifications', \App\Http\Controllers\Formation\QualificationController::class);
        Route::post('formations/{formation}/qualifications/{qualification}/approve', [\App\Http\Controllers\Formation\QualificationController::class, 'approve'])->name('qualifications.approve');
        Route::post('formations/{formation}/qualifications/{qualification}/reject', [\App\Http\Controllers\Formation\QualificationController::class, 'reject'])->name('qualifications.reject');

        // Diplomes routes
        Route::resource('formations.diplomes', \App\Http\Controllers\Formation\DiplomeController::class);
        Route::post('formations/{formation}/diplomes/{diplome}/deliver', [\App\Http\Controllers\Formation\DiplomeController::class, 'deliver'])->name('diplomes.deliver');
        Route::post('formations/{formation}/diplomes/{diplome}/reject', [\App\Http\Controllers\Formation\DiplomeController::class, 'reject'])->name('diplomes.reject');
        Route::post('formations/{formation}/diplomes/{diplome}/cancel', [\App\Http\Controllers\Formation\DiplomeController::class, 'cancel'])->name('diplomes.cancel');

        // Licences routes
        Route::resource('formations.licences', \App\Http\Controllers\Formation\LicenceController::class);
        Route::post('formations/{formation}/licences/{licence}/deliver', [\App\Http\Controllers\Formation\LicenceController::class, 'deliver'])->name('licences.deliver');
        Route::post('formations/{formation}/licences/{licence}/renew', [\App\Http\Controllers\Formation\LicenceController::class, 'renew'])->name('licences.renew');
        Route::post('formations/{formation}/licences/{licence}/suspend', [\App\Http\Controllers\Formation\LicenceController::class, 'suspend'])->name('licences.suspend');

        // Justificatifs routes
        Route::resource('formations.justificatifs', \App\Http\Controllers\Formation\JustificatifController::class);
        Route::post('formations/{formation}/justificatifs/{justificatif}/verify', [\App\Http\Controllers\Formation\JustificatifController::class, 'verify'])->name('justificatifs.verify');
        Route::post('formations/{formation}/justificatifs/{justificatif}/reject', [\App\Http\Controllers\Formation\JustificatifController::class, 'reject'])->name('justificatifs.reject');

        // Reports routes
        Route::get('reports/dashboard', [\App\Http\Controllers\Formation\ReportController::class, 'dashboard'])->name('reports.dashboard');
        Route::get('formations/{formation}/report', [\App\Http\Controllers\Formation\ReportController::class, 'formationReport'])->name('reports.formation');
        Route::get('formations/{formation}/situation-sce', [\App\Http\Controllers\Formation\ReportController::class, 'situationSCE'])->name('reports.situation-sce');
        Route::get('reports/qualification-rate', [\App\Http\Controllers\Formation\ReportController::class, 'qualificationRate'])->name('reports.qualification-rate');
        Route::get('reports/diplomation-rate', [\App\Http\Controllers\Formation\ReportController::class, 'diplomationRate'])->name('reports.diplomation-rate');
        Route::get('reports/licence-rate', [\App\Http\Controllers\Formation\ReportController::class, 'licenceRate'])->name('reports.licence-rate');
        Route::get('reports/attendance-rate', [\App\Http\Controllers\Formation\ReportController::class, 'attendanceRate'])->name('reports.attendance-rate');
        Route::get('formations/{formation}/report/export', [\App\Http\Controllers\Formation\ReportController::class, 'exportFormationReport'])->name('reports.export');
    });
