<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * RF-10: Dashboard consolidado – historial y resumen.
 */
class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // Última PA + clasificación
        $latestBP = $user->bloodPressureRecords()
            ->orderBy('measured_at', 'desc')
            ->first();

        $bpData = null;
        if ($latestBP) {
            $bpData = [
                'systolic'       => $latestBP->systolic,
                'diastolic'      => $latestBP->diastolic,
                'measured_at'    => $latestBP->measured_at,
                'classification' => BloodPressureRecordController::classify(
                    $latestBP->systolic,
                    $latestBP->diastolic
                ),
            ];
        }

        // Resumen semanal de PA
        $weeklyBP = $user->bloodPressureRecords()
            ->where('measured_at', '>=', Carbon::now()->startOfWeek())
            ->get();

        // Adherencia a medicamentos (última semana)
        $medications = $user->medications()->with(['logs' => function ($q) {
            $q->where('taken_at', '>=', Carbon::now()->startOfWeek());
        }])->get();

        $medTotal  = $medications->sum(fn ($m) => $m->logs->count());
        $medTaken  = $medications->sum(fn ($m) => $m->logs->where('status', 'tomado')->count());
        $adherence = $medTotal > 0 ? round(($medTaken / $medTotal) * 100, 1) : null;

        // Hábitos completados hoy
        $habitsToday = $user->habitLogs()
            ->whereDate('completed_at', Carbon::today())
            ->with('habit')
            ->get()
            ->pluck('habit.name');

        // Consumos de hoy
        $foodsToday = $user->foodLogs()
            ->whereDate('consumed_at', Carbon::today())
            ->with('food')
            ->count();

        // Plan activo
        $activePlan = $user->mealPlans()
            ->where('week_start', '<=', Carbon::today())
            ->orderBy('week_start', 'desc')
            ->first();

        return response()->json([
            'user' => [
                'name'      => $user->name,
                'hta_level' => $user->hta_level,
                'onboarding_completed' => $user->onboarding_completed,
            ],
            'blood_pressure' => [
                'latest'       => $bpData,
                'weekly_count' => $weeklyBP->count(),
                'weekly_avg'   => $weeklyBP->count() > 0 ? [
                    'systolic'  => round($weeklyBP->avg('systolic')),
                    'diastolic' => round($weeklyBP->avg('diastolic')),
                ] : null,
            ],
            'medication_adherence' => [
                'weekly_rate'  => $adherence,
                'total_meds'   => $medications->count(),
            ],
            'habits' => [
                'completed_today' => $habitsToday,
                'count_today'     => $habitsToday->count(),
            ],
            'nutrition' => [
                'foods_logged_today' => $foodsToday,
                'active_meal_plan'   => $activePlan ? [
                    'id'         => $activePlan->id,
                    'week_start' => $activePlan->week_start,
                ] : null,
            ],
            'disclaimer' => 'Esta aplicación no reemplaza la indicación médica. Consulte siempre a su profesional de salud.',
        ]);
    }

    /**
     * RF-10: Historial unificado con filtros de fecha.
     */
    public function history(Request $request)
    {
        $request->validate([
            'from' => 'sometimes|date',
            'to'   => 'sometimes|date',
        ]);

        /** @var \App\Models\User $user */
        $user = auth()->user();
        $from = $request->input('from', Carbon::now()->subMonth()->toDateString());
        $to   = $request->input('to', Carbon::today()->toDateString());

        $bpRecords = $user->bloodPressureRecords()
            ->whereBetween('measured_at', [$from, $to])
            ->orderBy('measured_at', 'desc')
            ->get()
            ->map(fn ($r) => array_merge($r->toArray(), [
                'classification' => BloodPressureRecordController::classify($r->systolic, $r->diastolic),
            ]));

        $foodLogs = $user->foodLogs()
            ->with('food')
            ->whereBetween('consumed_at', [$from, $to])
            ->orderBy('consumed_at', 'desc')
            ->get();

        $medications = $user->medications()->with(['logs' => function ($q) use ($from, $to) {
            $q->whereBetween('taken_at', [$from, $to])->orderBy('taken_at', 'desc');
        }])->get();

        $habitLogs = $user->habitLogs()
            ->with('habit')
            ->whereBetween('completed_at', [$from, $to])
            ->orderBy('completed_at', 'desc')
            ->get();

        return response()->json([
            'period' => ['from' => $from, 'to' => $to],
            'blood_pressure' => $bpRecords,
            'food_logs'      => $foodLogs,
            'medications'    => $medications,
            'habit_logs'     => $habitLogs,
        ]);
    }
}
