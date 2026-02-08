<?php

namespace App\Http\Controllers;

use App\Models\MealPlan;
use Illuminate\Http\Request;

class MealPlanController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $plans = $user->mealPlans()
            ->orderBy('week_start', 'desc')
            ->get();

        return response()->json($plans);
    }

    public function store(Request $request)
    {
        $request->validate([
            'week_start' => 'required|date',
            'notes'      => 'nullable|string',
        ]);

        /** @var \App\Models\User $user */
        $user = auth()->user();
        $plan = $user->mealPlans()->create($request->only([
            'week_start', 'notes'
        ]));

        return response()->json($plan, 201);
    }

    public function show(MealPlan $mealPlan)
    {
        if ($mealPlan->user_id !== auth()->id()) {
            abort(403, 'No autorizado');
        }
        return response()->json($mealPlan);
    }

    public function update(Request $request, MealPlan $mealPlan)
    {
        if ($mealPlan->user_id !== auth()->id()) {
            abort(403, 'No autorizado');
        }

        $request->validate([
            'week_start' => 'sometimes|date',
            'notes'      => 'nullable|string',
        ]);

        $mealPlan->update($request->only(['week_start', 'notes']));
        return response()->json($mealPlan);
    }

    public function destroy(MealPlan $mealPlan)
    {
        if ($mealPlan->user_id !== auth()->id()) {
            abort(403, 'No autorizado');
        }

        $mealPlan->delete();
        return response()->json(['message' => 'Plan eliminado']);
    }
}
