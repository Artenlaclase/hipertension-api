<?php

namespace App\Http\Controllers;

use App\Models\HydrationGoal;
use Illuminate\Http\Request;

class HydrationGoalController extends Controller
{
    public function show(Request $request)
    {
        $goal = $request->user()->hydrationGoals()
            ->where('effective_date', '<=', now()->toDateString())
            ->orderByDesc('effective_date')
            ->first();

        return response()->json([
            'data' => $goal,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'goal_ml' => 'required|integer|min:500|max:10000',
            'effective_date' => 'required|date',
        ]);

        $goal = $request->user()->hydrationGoals()->updateOrCreate(
            ['effective_date' => $validated['effective_date']],
            ['goal_ml' => $validated['goal_ml']]
        );

        return response()->json(['data' => $goal]);
    }
}
