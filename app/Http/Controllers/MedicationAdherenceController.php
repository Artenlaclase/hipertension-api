<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * RF-09.3 / RF-09.4: Estadísticas de adherencia a medicamentos.
 */
class MedicationAdherenceController extends Controller
{
    /**
     * Adherencia global del usuario.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $period = $request->input('period', 'monthly');

        $startDate = match ($period) {
            'weekly'  => Carbon::now()->startOfWeek(),
            'monthly' => Carbon::now()->startOfMonth(),
            default   => Carbon::now()->startOfMonth(),
        };

        $medications = $user->medications()->with(['logs' => function ($q) use ($startDate) {
            $q->where('taken_at', '>=', $startDate);
        }])->get();

        $adherenceData = $medications->map(function ($med) {
            $total   = $med->logs->count();
            $taken   = $med->logs->where('status', 'tomado')->count();
            $omitted = $med->logs->where('status', 'omitido')->count();
            $rate    = $total > 0 ? round(($taken / $total) * 100, 1) : 0;

            return [
                'medication_id'   => $med->id,
                'medication_name' => $med->name,
                'dosage'          => $med->dosage,
                'total_records'   => $total,
                'taken'           => $taken,
                'omitted'         => $omitted,
                'adherence_rate'  => $rate,
                'warning'         => $this->getWarning($rate, $omitted),
            ];
        });

        $globalTaken   = $adherenceData->sum('taken');
        $globalTotal   = $adherenceData->sum('total_records');
        $globalRate    = $globalTotal > 0 ? round(($globalTaken / $globalTotal) * 100, 1) : 0;

        return response()->json([
            'period'          => $period,
            'global_adherence' => $globalRate,
            'medications'     => $adherenceData,
            'message'         => $this->getGlobalMessage($globalRate),
        ]);
    }

    /**
     * RF-09.4: Mensajes informativos por omisiones frecuentes.
     * NO emite alertas médicas ni juicios clínicos.
     */
    private function getWarning(float $rate, int $omitted): ?string
    {
        if ($omitted >= 5) {
            return 'Se han registrado varias omisiones. Recuerda que la constancia en el tratamiento es importante para el control de la presión arterial.';
        }

        if ($rate < 50 && $rate > 0) {
            return 'Tu adherencia está por debajo del 50%. Configurar alarmas puede ayudarte a recordar tus tomas.';
        }

        if ($rate < 80 && $rate > 0) {
            return 'Intenta mejorar tu constancia. Cada toma cuenta para el control de tu presión.';
        }

        return null;
    }

    private function getGlobalMessage(float $rate): string
    {
        return match (true) {
            $rate >= 90 => '¡Excelente adherencia! Estás siguiendo tu tratamiento de forma consistente.',
            $rate >= 70 => 'Buena adherencia. Intenta no omitir tomas para mejores resultados.',
            $rate >= 50 => 'Tu adherencia puede mejorar. Las alarmas pueden ayudarte a recordar.',
            $rate > 0   => 'Tu adherencia es baja. Recuerda que la constancia es clave para controlar la presión arterial.',
            default     => 'No hay registros de tomas en este periodo. Empieza a registrar tu adherencia.',
        };
    }
}
