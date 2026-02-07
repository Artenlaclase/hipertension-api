<?php

namespace App\Http\Controllers;

use App\Models\FoodLog;
use Illuminate\Http\Request;

class FoodLogController extends Controller
{
    public function index()
    {
        $logs = auth()->user()->foodLogs()
            ->with('food')
            ->orderBy('consumed_at', 'desc')
            ->get();

        return response()->json($logs);
    }

    public function store(Request $request)
    {
        $request->validate([
            'food_id'     => 'required|exists:foods,id',
            'portion'     => 'required|string|max:255',
            'consumed_at' => 'required|date',
        ]);

        $log = auth()->user()->foodLogs()->create($request->only([
            'food_id', 'portion', 'consumed_at'
        ]));

        return response()->json($log->load('food'), 201);
    }

    public function destroy(FoodLog $foodLog)
    {
        if ($foodLog->user_id !== auth()->id()) {
            abort(403, 'No autorizado');
        }

        $foodLog->delete();
        return response()->json(['message' => 'Registro eliminado']);
    }
}
