<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SuperadminController;
use App\Http\Controllers\AgentController;
use Illuminate\Support\Facades\Route;

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Google Auth Routes
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
    Route::get('/auth/google/mock', [AuthController::class, 'showMockGooglePage'])->name('auth.google.mock');
    Route::post('/auth/google/mock', [AuthController::class, 'submitMockGoogle'])->name('auth.google.mock.submit');
    Route::get('/register/google/complete', [AuthController::class, 'showGoogleCompleteForm'])->name('register.google.complete');
    Route::post('/register/google/complete', [AuthController::class, 'submitGoogleComplete'])->name('register.google.complete.submit');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // 2FA Routes (accessible without passing 2fa check)
    Route::get('/two-factor', [AuthController::class, 'showTwoFactorForm'])->name('two-factor.index');
    Route::post('/two-factor', [AuthController::class, 'verifyTwoFactor'])->name('two-factor.verify');
    Route::post('/two-factor/resend', [AuthController::class, 'resendTwoFactor'])->name('two-factor.resend');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // 2FA Protected Routes
    Route::middleware('2fa')->group(function () {
        Route::get('/wilayah/search', [\App\Http\Controllers\WilayahController::class, 'search'])->name('wilayah.search');
        Route::get('/dashboard', [AuthController::class, 'dashboardRedirect'])->name('dashboard');

        // Superadmin Portal (/superadmin)
        Route::middleware('role:superadmin,admin_haji')->prefix('superadmin')->name('superadmin.')->group(function () {
            Route::get('/', [SuperadminController::class, 'index'])->name('index');

            // Setup & Settings (Superadmin only)
            Route::middleware('role:superadmin')->group(function () {
                Route::post('/settings', [SuperadminController::class, 'updateSettings'])->name('settings.update');
                Route::post('/levels', [SuperadminController::class, 'updateLevels'])->name('levels.update');
                Route::post('/backgrounds', [SuperadminController::class, 'uploadBackground'])->name('backgrounds.upload');
                Route::post('/backgrounds/{background}/delete', [SuperadminController::class, 'deleteBackground'])->name('backgrounds.delete');
                
                // User Management
                Route::post('/users', [SuperadminController::class, 'storeUser'])->name('users.store');
                Route::post('/users/{user}/update', [SuperadminController::class, 'updateUser'])->name('users.update');
                Route::post('/users/{user}/delete', [SuperadminController::class, 'deleteUser'])->name('users.delete');
            });

            // Operations & Gamification (Superadmin & Admin Haji)
            Route::post('/agents/{agent}/status', [SuperadminController::class, 'updateAgentStatus'])->name('agents.status');
            Route::post('/agents/{agent}/reject', [SuperadminController::class, 'rejectAgent'])->name('agents.reject');
            Route::post('/agents/{agent}/evaluate', [SuperadminController::class, 'evaluateAgentLevel'])->name('agents.evaluate');
            Route::post('/institutions/{institution}/status', [SuperadminController::class, 'updateInstitutionStatus'])->name('institutions.status');
            Route::delete('/agents/{agent}', [SuperadminController::class, 'deleteAgent'])->name('agents.delete');
            Route::post('/agents/{agent}/verify-credentials', [SuperadminController::class, 'verifyAgentCredentials'])->name('agents.verify-credentials');

            // Gamification, Racing & Incentive Management
            Route::post('/gamification/referral-program', [SuperadminController::class, 'storeReferralProgram'])->name('gamification.referral');
            Route::post('/gamification/point-settings', [SuperadminController::class, 'updatePointSettings'])->name('gamification.points');
            Route::post('/gamification/racing-program', [SuperadminController::class, 'storeRacingProgram'])->name('gamification.racing');
            Route::post('/gamification/incentive-settings', [SuperadminController::class, 'updateIncentiveSettings'])->name('gamification.incentive');
            Route::post('/gamification/siskehat-sync', [SuperadminController::class, 'syncSiskehatData'])->name('gamification.siskehat');

            // Agent CSV Import
            Route::get('/agents/import-template', [SuperadminController::class, 'downloadAgentTemplate'])->name('agents.import-template');
            Route::post('/agents/import-validate', [SuperadminController::class, 'validateImportAgents'])->name('agents.import-validate');
            Route::post('/agents/import-process', [SuperadminController::class, 'processImportAgents'])->name('agents.import-process');
        });

        // Dedicated Admin Haji Portal (/admin-haji)
        Route::middleware('role:admin_haji')->prefix('admin-haji')->name('admin-haji.')->group(function () {
            Route::get('/', [SuperadminController::class, 'index'])->name('index');
        });

        // Verification Center Routes (accessible to authenticated users without verified credentials)
        Route::get('/verify-credentials', [AuthController::class, 'showVerifyCredentialsForm'])->name('verify-credentials.index');
        Route::post('/verify-credentials/email', [AuthController::class, 'verifyEmailOtp'])->name('verify-credentials.email');
        Route::post('/verify-credentials/whatsapp', [AuthController::class, 'verifyWhatsappOtp'])->name('verify-credentials.whatsapp');
        Route::post('/verify-credentials/resend', [AuthController::class, 'resendVerificationOtp'])->name('verify-credentials.resend');

        // Agent routes protected by verification status
        Route::middleware('verified.credentials')->group(function () {
            Route::post('/agent/verify-nik', [AgentController::class, 'verifyNik'])->name('agent.verify-nik');
            Route::get('/agent/prospects/import-template', [AgentController::class, 'downloadImportTemplate'])->name('agent.prospects.import-template');
            Route::post('/agent/prospects/import-validate', [AgentController::class, 'validateImportProspects'])->name('agent.prospects.import-validate');
            Route::post('/agent/prospects/import-process', [AgentController::class, 'processImportProspects'])->name('agent.prospects.import-process');
            Route::post('/agent/prospects', [AgentController::class, 'storeProspect'])->name('agent.prospects.store');
            Route::post('/agent/prospects/{prospect}/update', [AgentController::class, 'updateProspect'])->name('agent.prospects.update');
            Route::post('/agent/prospects/{prospect}/delete', [AgentController::class, 'deleteProspect'])->name('agent.prospects.delete');

            // B2B Institutional Agent Dashboard
            Route::middleware('role:agent')->group(function () {
                Route::get('/agent/institution', [AgentController::class, 'institutionDashboard'])->name('agent.institution');
                Route::get('/agent/freelance', [AgentController::class, 'freelanceDashboard'])->name('agent.freelance');
                Route::get('/agent/verification-wizard', [AgentController::class, 'showVerificationWizard'])->name('agent.verification.wizard');
                Route::post('/agent/verification-wizard', [AgentController::class, 'submitVerificationWizard'])->name('agent.verification.submit');
                Route::post('/agent/institution/verify/{agent_id}', [AgentController::class, 'verifyInstitutionAgent'])->name('agent.institution.verify');
            });
        });

        // Agent Profile page (accessible by referral code with proper permissions check)
        Route::get('/agent/profile/{referral_code}', [AgentController::class, 'showProfileByReferral'])->name('agent.profile');
    });
});
