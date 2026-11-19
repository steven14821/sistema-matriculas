<?php

use App\Http\Controllers\Admin\AcademicOverviewController;
use App\Http\Controllers\Admin\StudentOverviewController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OfertaAcademicaController;
use App\Http\Controllers\Staff\AsignaturaController;
use App\Http\Controllers\Staff\EnrollmentController as StaffEnrollmentController;
use App\Http\Controllers\Staff\GrupoController;
use App\Http\Controllers\Staff\PeriodoController;
use App\Http\Controllers\Staff\ProfesorController;
use App\Http\Controllers\Staff\ProgramaController;
use App\Http\Controllers\Staff\SolicitudProgramaController as StaffSolicitudProgramaController;
use App\Http\Controllers\Student\EnrollmentController;
use App\Http\Controllers\Student\SolicitudProgramaController as StudentSolicitudProgramaController;
use App\Models\Asignatura;
use App\Models\Grupo;
use App\Models\Programa;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;

Route::get('/', function (): View {
    $programas = Programa::query()
        ->withCount('asignaturas')
        ->orderBy('nombre')
        ->get();

    $asignaturas = Asignatura::query()
        ->with('programa')
        ->orderBy('nombre')
        ->get();

    $grupos = Grupo::query()
        ->with(['asignatura', 'periodo', 'profesor'])
        ->whereHas('periodo', fn ($query) => $query->where('abierto', true))
        ->orderBy('id')
        ->get();

    return view('landing', [
        'programas' => $programas,
        'asignaturas' => $asignaturas,
        'grupos' => $grupos,
    ]);
})->name('home');

Route::get('/dashboard', DashboardController::class)
    ->middleware('auth')
    ->name('dashboard');

Route::get('/oferta', [OfertaAcademicaController::class, 'index'])
    ->middleware('auth')
    ->name('oferta.index');

Route::middleware(['auth', 'can:self-enroll'])
    ->group(function (): void {
        Route::get('/student/enrollments', [EnrollmentController::class, 'index'])->name('student.enrollments.index');
        Route::post('/student/groups/{grupo}/enrollments', [EnrollmentController::class, 'store'])->name('student.enrollments.store');
        Route::post('/student/solicitudes-programa', [StudentSolicitudProgramaController::class, 'store'])->name('student.solicitudes_programa.store');
        Route::get('/matriculas', [EnrollmentController::class, 'index'])->name('matriculas.mine');
        Route::post('/matriculas/grupos/{grupo}', [EnrollmentController::class, 'store'])->name('matriculas.inscribir');
        Route::delete('/matriculas/{matricula}', [EnrollmentController::class, 'destroy'])->name('matriculas.destroy');
    });

Route::middleware(['auth', 'can:manage-users'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::resource('users', UserController::class)->only(['index', 'create', 'store']);
        Route::patch('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.role.update');
        Route::get('/estudiantes', [StudentOverviewController::class, 'index'])->name('estudiantes.index');
        Route::get('/estudiantes/{estudiante}', [StudentOverviewController::class, 'show'])->name('estudiantes.show');
        Route::get('/catalogo', [AcademicOverviewController::class, 'index'])->name('catalogo.index');
    });

Route::middleware(['auth', 'can:manage-academic'])
    ->prefix('staff')
    ->name('staff.')
    ->group(function (): void {
        Route::resource('programas', ProgramaController::class)->except('show');
        Route::resource('asignaturas', AsignaturaController::class)->except('show');
        Route::resource('periodos', PeriodoController::class)->except('show');
        Route::resource('grupos', GrupoController::class)->except('show');
        Route::resource('profesores', ProfesorController::class)
            ->parameters(['profesores' => 'profesor'])
            ->except('show');
        Route::get('/solicitudes-programa', [StaffSolicitudProgramaController::class, 'index'])->name('solicitudes_programa.index');
        Route::get('/solicitudes-programa/{solicitudPrograma}/comprobante', [StaffSolicitudProgramaController::class, 'receipt'])->name('solicitudes_programa.comprobante');
        Route::patch('/solicitudes-programa/{solicitudPrograma}', [StaffSolicitudProgramaController::class, 'update'])->name('solicitudes_programa.update');
    });

Route::middleware(['auth', 'can:manage-enrollments'])
    ->prefix('staff')
    ->name('staff.')
    ->group(function (): void {
        Route::resource('enrollments', StaffEnrollmentController::class)->only(['index', 'create', 'store', 'destroy']);
    });
