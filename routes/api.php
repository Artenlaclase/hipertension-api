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
use App\Http\Controllers\MedicationAdherenceController;
use App\Http\Controllers\EducationalContentController;
use App\Http\Controllers\HabitController;
use App\Http\Controllers\HabitLogController;
use App\Http\Controllers\HabitStreakController;
use App\Http\Controllers\NutritionalRecommendationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InfusionController;
use App\Http\Controllers\HydrationLogController;

/*
|--------------------------------------------------------------------------
| API Routes – Hipertensión App
|--------------------------------------------------------------------------
*/

// ── Auth (público) ──────────────────────────────────────────────────
Route::post('register', [AuthController::class, 'register']);
Route::post('login',    [AuthController::class, 'login']);

// ── Disclaimer público (RNF-05) ─────────────────────────────────────
Route::get('disclaimer', function () {
    return response()->json([
        'message' => 'Esta aplicación no reemplaza la indicación médica profesional. '
            . 'Es una herramienta de apoyo y educación. '
            . 'Consulte siempre a su médico para decisiones sobre su tratamiento.',
    ]);
});

// ── Rutas protegidas con JWT ────────────────────────────────────────
Route::middleware('auth:api')->group(function () {

    // ── Auth / perfil ───────────────────────────────────────────────
    Route::get('me',              [AuthController::class, 'me']);
    Route::post('logout',         [AuthController::class, 'logout']);
    Route::post('refresh',        [AuthController::class, 'refresh']);
    Route::put('profile',         [AuthController::class, 'updateProfile']);
    Route::post('onboarding',     [AuthController::class, 'onboarding']);       // RF-01

    // ── Dashboard (RF-10) ───────────────────────────────────────────
    Route::get('dashboard',       [DashboardController::class, 'index']);
    Route::get('history',         [DashboardController::class, 'history']);

    // ── Presión arterial (RF-02, RF-03) ─────────────────────────────
    Route::apiResource('blood-pressure', BloodPressureRecordController::class)
        ->only(['index', 'store', 'show', 'destroy']);
    Route::get('blood-pressure-stats', [BloodPressureRecordController::class, 'statistics']); // RF-02

    // ── Recomendaciones nutricionales (RF-05) ───────────────────────
    Route::get('nutritional-recommendations', [NutritionalRecommendationController::class, 'index']);

    // ── Alimentos / catálogo (RF-06) ────────────────────────────────
    Route::apiResource('foods', FoodController::class)
        ->only(['index', 'show', 'store']);

    // ── Registro de consumo (RF-06) ─────────────────────────────────
    Route::apiResource('food-logs', FoodLogController::class)
        ->only(['index', 'store', 'destroy']);

    // ── Plan alimenticio (RF-04) ────────────────────────────────────
    Route::apiResource('meal-plans', MealPlanController::class);

    // ── Medicamentos (RF-09.1) ──────────────────────────────────────
    Route::apiResource('medications', MedicationController::class);

    // ── Alarmas de medicamentos (RF-09.2) ───────────────────────────
    Route::post('medications/{medication}/alarms',           [MedicationAlarmController::class, 'store']);
    Route::put('medication-alarms/{medication_alarm}',       [MedicationAlarmController::class, 'update']);
    Route::delete('medication-alarms/{medication_alarm}',    [MedicationAlarmController::class, 'destroy']);

    // ── Registro de toma (RF-09.3) ──────────────────────────────────
    Route::get('medications/{medication}/logs',  [MedicationLogController::class, 'index']);
    Route::post('medications/{medication}/logs', [MedicationLogController::class, 'store']);

    // ── Adherencia a medicamentos (RF-09.4) ─────────────────────────
    Route::get('medication-adherence', [MedicationAdherenceController::class, 'index']);

    // ── Contenido educativo (RF-07) ─────────────────────────────────
    Route::apiResource('educational-contents', EducationalContentController::class)
        ->only(['index', 'show']);

    // ── Hábitos / catálogo ──────────────────────────────────────────
    Route::apiResource('habits', HabitController::class)
        ->only(['index', 'show']);

    // ── Seguimiento de hábitos (RF-08) ──────────────────────────────
    Route::apiResource('habit-logs', HabitLogController::class)
        ->only(['index', 'store', 'destroy']);

    // ── Rachas de hábitos (RF-08) ───────────────────────────────────
    Route::get('habit-streaks',          [HabitStreakController::class, 'index']);
    Route::get('habit-streaks/{habit}',  [HabitStreakController::class, 'show']);

    // ── Infusiones / catálogo (Hidratación) ─────────────────────────
    Route::get('infusions',              [InfusionController::class, 'index']);
    Route::get('infusions/{infusion}',   [InfusionController::class, 'show']);
    Route::post('infusions',             [InfusionController::class, 'store']);

    // ── Registro de hidratación ─────────────────────────────────────
    Route::get('hydration-logs',         [HydrationLogController::class, 'index']);
    Route::post('hydration-logs',        [HydrationLogController::class, 'store']);
    Route::delete('hydration-logs/{hydration_log}', [HydrationLogController::class, 'destroy']);
    Route::get('hydration-summary',      [HydrationLogController::class, 'summary']);
});
