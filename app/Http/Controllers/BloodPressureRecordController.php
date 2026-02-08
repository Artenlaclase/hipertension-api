<?php

namespace App\Http\Controllers;

use App\Models\BloodPressureRecord;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BloodPressureRecordController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $records = $user->bloodPressureRecords()
            ->orderBy('measured_at', 'desc')
            ->get()
            ->map(fn ($r) => array_merge($r->toArray(), [
                'classification' => self::classify($r->systolic, $r->diastolic),
            ]));

        return response()->json($records);
    }

    public function store(Request $request)
    {
        $request->validate([
            'systolic'    => 'required|integer|min:50|max:300',
            'diastolic'   => 'required|integer|min:30|max:200',
            'measured_at' => 'required|date',
        ]);

        /** @var \App\Models\User $user */
        $user = auth()->user();
        $record = $user->bloodPressureRecords()->create($request->only([
            'systolic', 'diastolic', 'measured_at'
        ]));

        $classification = self::classify($record->systolic, $record->diastolic);

        return response()->json(array_merge($record->toArray(), [
            'classification' => $classification,
        ]), 201);
    }

    public function show(BloodPressureRecord $bloodPressureRecord)
    {
        $this->authorizeUser($bloodPressureRecord);

        return response()->json(array_merge($bloodPressureRecord->toArray(), [
            'classification' => self::classify(
                $bloodPressureRecord->systolic,
                $bloodPressureRecord->diastolic
            ),
        ]));
    }

    public function destroy(BloodPressureRecord $bloodPressureRecord)
    {
        $this->authorizeUser($bloodPressureRecord);
        $bloodPressureRecord->delete();
        return response()->json(['message' => 'Registro eliminado']);
    }

    /**
     * RF-02: Estadísticas de PA por periodo (diario, semanal, mensual).
     * RF-03: Incluye clasificación semáforo en cada registro.
     */
    public function statistics(Request $request)
    {
        $request->validate([
            'period' => 'required|in:daily,weekly,monthly',
        ]);

        /** @var \App\Models\User $user */
        $user = auth()->user();
        $period = $request->period;

        $startDate = match ($period) {
            'daily'   => Carbon::today(),
            'weekly'  => Carbon::now()->startOfWeek(),
            'monthly' => Carbon::now()->startOfMonth(),
        };

        $records = $user->bloodPressureRecords()
            ->where('measured_at', '>=', $startDate)
            ->orderBy('measured_at', 'asc')
            ->get();

        if ($records->isEmpty()) {
            return response()->json([
                'period'  => $period,
                'count'   => 0,
                'records' => [],
                'average' => null,
                'latest_classification' => null,
            ]);
        }

        $avgSystolic  = round($records->avg('systolic'));
        $avgDiastolic = round($records->avg('diastolic'));
        $latest       = $records->last();

        return response()->json([
            'period'  => $period,
            'count'   => $records->count(),
            'average' => [
                'systolic'       => $avgSystolic,
                'diastolic'      => $avgDiastolic,
                'classification' => self::classify($avgSystolic, $avgDiastolic),
            ],
            'min_systolic'  => $records->min('systolic'),
            'max_systolic'  => $records->max('systolic'),
            'min_diastolic' => $records->min('diastolic'),
            'max_diastolic' => $records->max('diastolic'),
            'latest_classification' => self::classify($latest->systolic, $latest->diastolic),
            'records' => $records->map(fn ($r) => [
                'id'             => $r->id,
                'systolic'       => $r->systolic,
                'diastolic'      => $r->diastolic,
                'measured_at'    => $r->measured_at,
                'classification' => self::classify($r->systolic, $r->diastolic),
            ]),
        ]);
    }

    /**
     * RF-03: Clasificación semáforo de presión arterial.
     * Verde  = controlada (< 120/80)
     * Amarillo = elevada (120-139 / 80-89)
     * Rojo   = alta (≥ 140 / ≥ 90)
     */
    public static function classify(int $systolic, int $diastolic): array
    {
        if ($systolic < 120 && $diastolic < 80) {
            return [
                'level' => 'controlada',
                'color' => 'verde',
                'message' => 'Tu presión arterial está en rango normal. ¡Sigue así!',
            ];
        }

        if ($systolic < 140 && $diastolic < 90) {
            return [
                'level' => 'elevada',
                'color' => 'amarillo',
                'message' => 'Tu presión está elevada. Cuida tu alimentación y reduce el sodio.',
            ];
        }

        return [
            'level' => 'alta',
            'color' => 'rojo',
            'message' => 'Tu presión está alta. Consulta a tu médico y revisa tu tratamiento.',
        ];
    }

    private function authorizeUser(BloodPressureRecord $record)
    {
        if ($record->user_id !== auth()->id()) {
            abort(403, 'No autorizado');
        }
    }
}
