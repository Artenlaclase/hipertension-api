<?php

namespace App\Http\Controllers;

use App\Models\Medication;
use App\Models\MedicationLog;
use Illuminate\Http\Request;

class MedicationLogController extends Controller
{
    public function index(Medication $medication)
    {
        if ($medication->user_id !== auth()->id()) {
            abort(403, 'No autorizado');
        }

        return response()->json(
            $medication->logs()->orderBy('taken_at', 'desc')->get()
        );
    }

    public function store(Request $request, Medication $medication)
    {
        if ($medication->user_id !== auth()->id()) {
            abort(403, 'No autorizado');
        }

        $request->validate([
            'taken_at' => 'required|date',
            'status'   => 'required|string|in:tomado,omitido',
        ]);

        $log = $medication->logs()->create($request->only(['taken_at', 'status']));

        return response()->json($log, 201);
    }
}
