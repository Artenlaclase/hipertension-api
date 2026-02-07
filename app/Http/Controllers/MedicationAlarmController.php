<?php

namespace App\Http\Controllers;

use App\Models\Medication;
use App\Models\MedicationAlarm;
use Illuminate\Http\Request;

class MedicationAlarmController extends Controller
{
    public function store(Request $request, Medication $medication)
    {
        if ($medication->user_id !== auth()->id()) {
            abort(403, 'No autorizado');
        }

        $request->validate([
            'alarm_time'   => 'required|date_format:H:i',
            'days_of_week' => 'required|string|max:255',
            'active'       => 'sometimes|boolean',
        ]);

        $alarm = $medication->alarms()->create($request->only([
            'alarm_time', 'days_of_week', 'active'
        ]));

        return response()->json($alarm, 201);
    }

    public function update(Request $request, MedicationAlarm $medicationAlarm)
    {
        $this->authorizeUser($medicationAlarm);

        $request->validate([
            'alarm_time'   => 'sometimes|date_format:H:i',
            'days_of_week' => 'sometimes|string|max:255',
            'active'       => 'sometimes|boolean',
        ]);

        $medicationAlarm->update($request->only(['alarm_time', 'days_of_week', 'active']));
        return response()->json($medicationAlarm);
    }

    public function destroy(MedicationAlarm $medicationAlarm)
    {
        $this->authorizeUser($medicationAlarm);
        $medicationAlarm->delete();
        return response()->json(['message' => 'Alarma eliminada']);
    }

    private function authorizeUser(MedicationAlarm $alarm)
    {
        if ($alarm->medication->user_id !== auth()->id()) {
            abort(403, 'No autorizado');
        }
    }
}
