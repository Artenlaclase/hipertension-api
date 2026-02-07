<?php

namespace App\Http\Controllers;

use App\Models\HydrationLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class HydrationLogController extends Controller
{
    /**
     * Listado de registros de hidratación del usuario.
     *
     * GET /api/hydration-logs?date=2026-02-07&type=water|infusion|other
     */
    public function index(Request $request): JsonResponse
    {
        $query = $request->user()->hydrationLogs()
            ->with('infusion:id,name,precaution_level')
            ->orderByDesc('logged_at');

        if ($request->filled('date')) {
            $query->whereDate('logged_at', $request->date);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        return response()->json(['data' => $query->get()]);
    }

    /**
     * Registrar ingesta de líquido.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type'         => 'required|in:water,infusion,other',
            'infusion_id'  => 'nullable|required_if:type,infusion|exists:infusions,id',
            'amount_ml'    => 'required|integer|min:50|max:2000',
            'logged_at'    => 'nullable|date',
            'notes'        => 'nullable|string|max:500',
        ]);

        // Si es infusión, verificar nivel de precaución y advertir
        $warning = null;
        if ($data['type'] === 'infusion' && !empty($data['infusion_id'])) {
            $infusion = \App\Models\Infusion::find($data['infusion_id']);

            if ($infusion && $infusion->precaution_level === 'avoid') {
                return response()->json([
                    'message' => 'Esta infusión está marcada como "evitar" para personas con hipertensión.',
                    'precaution_note' => $infusion->precaution_note,
                    'suggestion' => 'Consulte a su médico antes de consumirla.',
                ], 422);
            }

            if ($infusion && $infusion->precaution_level === 'caution') {
                $warning = [
                    'level'   => 'caution',
                    'message' => "⚠️ {$infusion->name}: consumir con moderación.",
                    'note'    => $infusion->precaution_note,
                    'max_daily_cups' => $infusion->max_daily_cups,
                ];
            }
        }

        $log = $request->user()->hydrationLogs()->create(array_merge($data, [
            'logged_at' => $data['logged_at'] ?? now(),
        ]));

        $log->load('infusion:id,name,precaution_level');

        $response = ['data' => $log, 'message' => 'Registro de hidratación guardado.'];
        if ($warning) {
            $response['warning'] = $warning;
        }

        return response()->json($response, 201);
    }

    /**
     * Eliminar registro.
     */
    public function destroy(Request $request, HydrationLog $hydrationLog): JsonResponse
    {
        if ($hydrationLog->user_id !== $request->user()->id) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $hydrationLog->delete();

        return response()->json(['message' => 'Registro eliminado.']);
    }

    /**
     * Resumen diario de hidratación.
     *
     * GET /api/hydration-summary?date=2026-02-07
     */
    public function summary(Request $request): JsonResponse
    {
        $date = $request->input('date', now()->toDateString());
        $user = $request->user();

        $logs = $user->hydrationLogs()
            ->whereDate('logged_at', $date)
            ->with('infusion:id,name,precaution_level')
            ->get();

        $totalMl = $logs->sum('amount_ml');
        $goalMl  = 2000; // Meta diaria recomendada (2 litros)

        $byType = $logs->groupBy('type')->map(fn ($group) => [
            'count'    => $group->count(),
            'total_ml' => $group->sum('amount_ml'),
        ]);

        // Infusiones consumidas hoy con detalle
        $infusionsToday = $logs->where('type', 'infusion')
            ->groupBy('infusion_id')
            ->map(function ($group) {
                $infusion = $group->first()->infusion;
                return [
                    'name'             => $infusion->name ?? 'Desconocida',
                    'precaution_level' => $infusion->precaution_level ?? null,
                    'cups'             => $group->count(),
                    'total_ml'         => $group->sum('amount_ml'),
                    'max_daily_cups'   => $infusion->max_daily_cups ?? null,
                ];
            })->values();

        // Alertas por exceso de tazas
        $alerts = [];
        foreach ($infusionsToday as $inf) {
            if ($inf['max_daily_cups'] && $inf['cups'] >= $inf['max_daily_cups']) {
                $alerts[] = "⚠️ Has alcanzado el máximo recomendado de {$inf['name']} ({$inf['max_daily_cups']} tazas/día).";
            }
        }

        $percentage = $goalMl > 0 ? round(($totalMl / $goalMl) * 100) : 0;

        return response()->json([
            'date'       => $date,
            'total_ml'   => $totalMl,
            'goal_ml'    => $goalMl,
            'percentage' => min($percentage, 100),
            'on_track'   => $totalMl >= $goalMl,
            'by_type'    => $byType,
            'infusions_today' => $infusionsToday,
            'alerts'     => $alerts,
            'tip'        => $totalMl < 1000
                ? '💧 Recuerda hidratarte. La hidratación adecuada ayuda a regular la presión arterial.'
                : ($totalMl >= $goalMl
                    ? '✅ ¡Excelente! Has cumplido tu meta de hidratación hoy.'
                    : '💧 Vas bien, sigue hidratándote para cumplir tu meta diaria.'),
        ]);
    }
}
