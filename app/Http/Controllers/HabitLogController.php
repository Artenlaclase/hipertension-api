<?php

namespace App\Http\Controllers;

use App\Models\HabitLog;
use Illuminate\Http\Request;

class HabitLogController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $logs = $user->habitLogs()
            ->with('habit')
            ->orderBy('completed_at', 'desc')
            ->get();

        return response()->json($logs);
    }

    public function store(Request $request)
    {
        $request->validate([
            'habit_id'     => 'required|exists:habits,id',
            'completed_at' => 'required|date',
        ]);

        /** @var \App\Models\User $user */
        $user = auth()->user();
        $log = $user->habitLogs()->create($request->only([
            'habit_id', 'completed_at'
        ]));

        return response()->json($log->load('habit'), 201);
    }

    public function destroy(HabitLog $habitLog)
    {
        if ($habitLog->user_id !== auth()->id()) {
            abort(403, 'No autorizado');
        }

        $habitLog->delete();
        return response()->json(['message' => 'Registro eliminado']);
    }
}
