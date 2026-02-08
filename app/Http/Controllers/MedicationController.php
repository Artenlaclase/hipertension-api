<?php

namespace App\Http\Controllers;

use App\Models\Medication;
use Illuminate\Http\Request;

class MedicationController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $medications = $user->medications()
            ->with('alarms')
            ->get();

        return response()->json($medications);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'dosage'       => 'required|string|max:255',
            'instructions' => 'nullable|string',
        ]);

        /** @var \App\Models\User $user */
        $user = auth()->user();
        $medication = $user->medications()->create($request->only([
            'name', 'dosage', 'instructions'
        ]));

        return response()->json($medication, 201);
    }

    public function show(Medication $medication)
    {
        $this->authorizeUser($medication);
        return response()->json($medication->load(['alarms', 'logs']));
    }

    public function update(Request $request, Medication $medication)
    {
        $this->authorizeUser($medication);

        $request->validate([
            'name'         => 'sometimes|string|max:255',
            'dosage'       => 'sometimes|string|max:255',
            'instructions' => 'nullable|string',
        ]);

        $medication->update($request->only(['name', 'dosage', 'instructions']));
        return response()->json($medication);
    }

    public function destroy(Medication $medication)
    {
        $this->authorizeUser($medication);
        $medication->delete();
        return response()->json(['message' => 'Medicamento eliminado']);
    }

    private function authorizeUser(Medication $medication)
    {
        if ($medication->user_id !== auth()->id()) {
            abort(403, 'No autorizado');
        }
    }
}
