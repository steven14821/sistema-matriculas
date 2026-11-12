<?php

use App\Http\Controllers\Admin\AsignaturaController;
use App\Http\Controllers\Admin\GrupoController;
use App\Http\Controllers\Admin\PeriodoController;
use App\Http\Controllers\Admin\ProfesorController;
use App\Http\Controllers\Admin\ProgramaController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MatriculaController;
use App\Http\Controllers\OfertaAcademicaController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'landing')->name('home');

Route::get('/dashboard', DashboardController::class)
    ->middleware('auth')
    ->name('dashboard');

Route::middleware(['auth'])
    ->group(function (): void {
        Route::get('/oferta', [OfertaAcademicaController::class, 'index'])->name('oferta.index');
        Route::get('/matriculas', [MatriculaController::class, 'mine'])->name('matriculas.mine');
        Route::post('/matriculas/grupos/{grupo}', [MatriculaController::class, 'inscribir'])->name('matriculas.inscribir');
        Route::delete('/matriculas/{matricula}', [MatriculaController::class, 'destroy'])->name('matriculas.destroy');
    });

Route::middleware(['auth', 'can:manage-professors'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('/grupos', [GrupoController::class, 'index'])->name('grupos.index');
        Route::put('/grupos/{grupo}/profesor', [GrupoController::class, 'assignProfessor'])->name('grupos.assign-professor');
        Route::resource('profesores', ProfesorController::class)
            ->parameters(['profesores' => 'profesor'])
            ->except('show');
    });

Route::middleware(['auth', 'can:manage-matriculas'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::resource('matriculas', MatriculaController::class)->only(['index', 'create', 'store', 'destroy']);
    });

Route::middleware(['auth', 'can:manage-users'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::resource('users', UserController::class)->except('show');
    });

Route::middleware(['auth', 'can:manage-academic-data'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::resource('programas', ProgramaController::class)->except('show');
        Route::resource('asignaturas', AsignaturaController::class)->except('show');
        Route::resource('periodos', PeriodoController::class)->except('show');
        Route::resource('grupos', GrupoController::class)->except(['show', 'index']);
    });
