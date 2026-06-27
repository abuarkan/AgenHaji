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
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [AuthController::class, 'dashboardRedirect'])->name('dashboard');

    // Superadmin & Admin Haji Portal
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

        // Operations (Admin Haji only)
        Route::middleware('role:admin_haji')->group(function () {
            Route::post('/agents/{agent}/status', [SuperadminController::class, 'updateAgentStatus'])->name('agents.status');
            Route::post('/agents/{agent}/evaluate', [SuperadminController::class, 'evaluateAgentLevel'])->name('agents.evaluate');
            Route::post('/institutions/{institution}/status', [SuperadminController::class, 'updateInstitutionStatus'])->name('institutions.status');
        });
    });

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

    // Agent Profile page (accessible by referral code with proper permissions check)
    Route::get('/agent/profile/{referral_code}', [AgentController::class, 'showProfileByReferral'])->name('agent.profile');
});
