<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HydrationStatsController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $period = $request->query('period', 'week');
        $start = now()->startOfWeek();
        $end = now()->endOfWeek();
        if ($period === 'month') {
            $start = now()->startOfMonth();
            $end = now()->endOfMonth();
        }

        $records = $user->hydrationRecords()
            ->whereBetween('recorded_at', [$start, $end])
            ->get();

        $goal = $user->hydrationGoals()
            ->where('effective_date', '<=', now()->toDateString())
            ->orderByDesc('effective_date')
            ->first();
        $goal_ml = $goal?->goal_ml ?? 2000;

        $byType = $records->groupBy('liquid_type')->map->sum('amount_ml');
        $daily = $records->groupBy(fn($r) => $r->recorded_at->toDateString())
            ->map(fn($g) => ['date' => $g->first()->recorded_at->toDateString(), 'total_ml' => $g->sum('amount_ml')])
            ->values();
        $days_goal_reached = $daily->filter(fn($d) => $d['total_ml'] >= $goal_ml)->count();

        return response()->json([
            'data' => [
                'period' => $period,
                'daily_average_ml' => $daily->avg('total_ml'),
                'goal_ml' => $goal_ml,
                'days_goal_reached' => $days_goal_reached,
                'total_days' => $daily->count(),
                'by_type' => $byType,
                'daily' => $daily,
            ]
        ]);
    }
}
