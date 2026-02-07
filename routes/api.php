<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BloodPressureRecordController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\FoodLogController;
use App\Http\Controllers\MealPlanController;
use App\Http\Controllers\MedicationController;
use App\Http\Controllers\MedicationAlarmController;
use App\Http\Controllers\MedicationLogController;
use App\Http\Controllers\EducationalContentController;
use App\Http\Controllers\HabitController;
use App\Http\Controllers\HabitLogController;

/*
|--------------------------------------------------------------------------
| API Routes – Hipertensión App
|--------------------------------------------------------------------------
*/

// ── Auth (público) ──────────────────────────────────────────────────
Route::post('register', [AuthController::class, 'register']);
Route::post('login',    [AuthController::class, 'login']);

// ── Rutas protegidas con JWT ────────────────────────────────────────
Route::middleware('auth:api')->group(function () {

    // Auth / perfil
    Route::get('me',              [AuthController::class, 'me']);
    Route::post('logout',         [AuthController::class, 'logout']);
    Route::post('refresh',        [AuthController::class, 'refresh']);
    Route::put('profile',         [AuthController::class, 'updateProfile']);

    // Presión arterial
    Route::apiResource('blood-pressure', BloodPressureRecordController::class)
        ->only(['index', 'store', 'show', 'destroy']);

    // Alimentos (catálogo)
    Route::apiResource('foods', FoodController::class)
        ->only(['index', 'show', 'store']);

    // Registro de consumo
    Route::apiResource('food-logs', FoodLogController::class)
        ->only(['index', 'store', 'destroy']);

    // Plan alimenticio
    Route::apiResource('meal-plans', MealPlanController::class);

    // Medicamentos
    Route::apiResource('medications', MedicationController::class);

    // Alarmas de medicamentos
    Route::post('medications/{medication}/alarms',           [MedicationAlarmController::class, 'store']);
    Route::put('medication-alarms/{medication_alarm}',       [MedicationAlarmController::class, 'update']);
    Route::delete('medication-alarms/{medication_alarm}',    [MedicationAlarmController::class, 'destroy']);

    // Registro de toma de medicamentos
    Route::get('medications/{medication}/logs',  [MedicationLogController::class, 'index']);
    Route::post('medications/{medication}/logs', [MedicationLogController::class, 'store']);

    // Contenido educativo
    Route::apiResource('educational-contents', EducationalContentController::class)
        ->only(['index', 'show']);

    // Hábitos (catálogo)
    Route::apiResource('habits', HabitController::class)
        ->only(['index', 'show']);

    // Seguimiento de hábitos
    Route::apiResource('habit-logs', HabitLogController::class)
        ->only(['index', 'store', 'destroy']);
});
