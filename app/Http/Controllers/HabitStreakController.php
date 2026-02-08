<?php

namespace App\Http\Controllers;

use App\Models\HabitLog;
use App\Models\Habit;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * RF-08: Gestión de hábitos – rachas y refuerzo positivo.
 */
class HabitStreakController extends Controller
{
    /**
     * Devuelve las rachas de todos los hábitos del usuario.
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $habits = Habit::all();

        $streaks = $habits->map(function ($habit) use ($user) {
            return $this->calculateStreak($user->id, $habit);
        });

        return response()->json([
            'streaks' => $streaks,
            'total_today' => $user->habitLogs()
                ->whereDate('completed_at', Carbon::today())
                ->count(),
            'message' => $this->getMotivationalMessage($streaks),
        ]);
    }

    /**
     * Racha de un hábito específico.
     */
    public function show(Habit $habit)
    {
        $streak = $this->calculateStreak(auth()->id(), $habit);
        return response()->json($streak);
    }

    private function calculateStreak(int $userId, Habit $habit): array
    {
        $logs = HabitLog::where('user_id', $userId)
            ->where('habit_id', $habit->id)
            ->orderBy('completed_at', 'desc')
            ->get()
            ->pluck('completed_at')
            ->map(fn ($d) => Carbon::parse($d)->toDateString())
            ->unique()
            ->values();

        // Calcular racha actual (días consecutivos)
        $currentStreak = 0;
        $checkDate = Carbon::today();

        foreach ($logs as $logDate) {
            if ($logDate === $checkDate->toDateString()) {
                $currentStreak++;
                $checkDate->subDay();
            } elseif ($logDate === $checkDate->subDay()->toDateString()) {
                // Permitir que no se haya registrado hoy aún
                $currentStreak++;
                $checkDate->subDay();
            } else {
                break;
            }
        }

        // Mejor racha histórica
        $bestStreak = $this->calculateBestStreak($logs);

        // Completado hoy?
        $completedToday = $logs->contains(Carbon::today()->toDateString());

        return [
            'habit_id'        => $habit->id,
            'habit_name'      => $habit->name,
            'current_streak'  => $currentStreak,
            'best_streak'     => $bestStreak,
            'completed_today' => $completedToday,
            'total_completions' => $logs->count(),
            'reinforcement'   => $this->getReinforcement($currentStreak),
        ];
    }

    private function calculateBestStreak($dates): int
    {
        if ($dates->isEmpty()) return 0;

        $best = 1;
        $current = 1;

        for ($i = 1; $i < $dates->count(); $i++) {
            $prev = Carbon::parse($dates[$i - 1]);
            $curr = Carbon::parse($dates[$i]);

            if ($prev->diffInDays($curr) === 1) {
                $current++;
                $best = max($best, $current);
            } else {
                $current = 1;
            }
        }

        return max($best, $current);
    }

    /**
     * RF-08: Refuerzo positivo según la racha.
     */
    private function getReinforcement(int $streak): string
    {
        return match (true) {
            $streak >= 30 => '🏆 ¡Increíble! Llevas un mes completo. ¡Eres un ejemplo!',
            $streak >= 14 => '🌟 ¡Dos semanas seguidas! Tu constancia está marcando la diferencia.',
            $streak >= 7  => '🔥 ¡Una semana completa! Tu corazón te lo agradece.',
            $streak >= 3  => '💪 ¡Tres días seguidos! Vas por buen camino.',
            $streak >= 1  => '✅ ¡Buen inicio! Cada día cuenta para tu salud.',
            default       => '🎯 ¡Hoy es un buen día para empezar!',
        };
    }

    private function getMotivationalMessage($streaks): string
    {
        $maxStreak = $streaks->max('current_streak');

        if ($maxStreak >= 7) {
            return '¡Excelente semana! Tus hábitos saludables están ayudando a controlar tu presión.';
        }

        if ($maxStreak >= 3) {
            return 'Vas muy bien. Recuerda que la constancia es clave para tu salud cardiovascular.';
        }

        return 'Cada pequeño paso cuenta. ¡Registra tus hábitos diarios para ver tu progreso!';
    }
}
