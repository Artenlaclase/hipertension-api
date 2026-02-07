<?php

namespace App\Http\Controllers;

use App\Models\BloodPressureRecord;
use Illuminate\Http\Request;

class BloodPressureRecordController extends Controller
{
    public function index()
    {
        $records = auth()->user()->bloodPressureRecords()
            ->orderBy('measured_at', 'desc')
            ->get();

        return response()->json($records);
    }

    public function store(Request $request)
    {
        $request->validate([
            'systolic'    => 'required|integer|min:50|max:300',
            'diastolic'   => 'required|integer|min:30|max:200',
            'measured_at' => 'required|date',
        ]);

        $record = auth()->user()->bloodPressureRecords()->create($request->only([
            'systolic', 'diastolic', 'measured_at'
        ]));

        return response()->json($record, 201);
    }

    public function show(BloodPressureRecord $bloodPressureRecord)
    {
        $this->authorizeUser($bloodPressureRecord);
        return response()->json($bloodPressureRecord);
    }

    public function destroy(BloodPressureRecord $bloodPressureRecord)
    {
        $this->authorizeUser($bloodPressureRecord);
        $bloodPressureRecord->delete();
        return response()->json(['message' => 'Registro eliminado']);
    }

    private function authorizeUser(BloodPressureRecord $record)
    {
        if ($record->user_id !== auth()->id()) {
            abort(403, 'No autorizado');
        }
    }
}
