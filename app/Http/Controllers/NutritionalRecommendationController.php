<?php

namespace App\Http\Controllers;

use App\Models\Food;
use Illuminate\Http\Request;

/**
 * RF-05: Recomendaciones nutricionales dinámicas.
 * Basado en la última lectura de PA del usuario, sugiere alimentos
 * y sustituciones según el modelo DASH.
 */
class NutritionalRecommendationController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Obtener última lectura de PA
        $latestBP = $user->bloodPressureRecords()
            ->orderBy('measured_at', 'desc')
            ->first();

        if (!$latestBP) {
            return response()->json([
                'message' => 'Registra tu primera medición de presión arterial para recibir recomendaciones.',
                'recommendations' => [],
                'substitutions' => [],
            ]);
        }

        $classification = BloodPressureRecordController::classify(
            $latestBP->systolic,
            $latestBP->diastolic
        );

        // Generar recomendaciones según nivel de PA
        $recommendations = $this->getRecommendations($classification['level']);
        $substitutions = $this->getSubstitutions($classification['level']);
        $suggestedFoods = $this->getSuggestedFoods($classification['level']);
        $avoidFoods = $this->getAvoidFoods($classification['level']);

        return response()->json([
            'blood_pressure' => [
                'systolic'       => $latestBP->systolic,
                'diastolic'      => $latestBP->diastolic,
                'classification' => $classification,
                'measured_at'    => $latestBP->measured_at,
            ],
            'recommendations' => $recommendations,
            'substitutions'   => $substitutions,
            'suggested_foods' => $suggestedFoods,
            'avoid_foods'     => $avoidFoods,
            'dash_tips'       => $this->getDashTips($classification['level']),
        ]);
    }

    private function getRecommendations(string $level): array
    {
        $base = [
            'Consume al menos 5 porciones de frutas y verduras al día.',
            'Prefiere cereales integrales sobre refinados.',
            'Incluye lácteos bajos en grasa en tu dieta.',
        ];

        return match ($level) {
            'controlada' => array_merge($base, [
                'Tu presión está bien. Mantén tus hábitos alimenticios actuales.',
                'Consume alimentos ricos en potasio como banano y espinaca.',
            ]),
            'elevada' => array_merge($base, [
                'Reduce tu consumo de sodio a menos de 2,300 mg/día.',
                'Aumenta el consumo de frutas ricas en potasio.',
                'Evita alimentos procesados y enlatados.',
                'Reduce el consumo de cafeína.',
            ]),
            'alta' => array_merge($base, [
                'URGENTE: Reduce el sodio a menos de 1,500 mg/día.',
                'Elimina por completo los alimentos procesados.',
                'Aumenta significativamente frutas, verduras y pescados.',
                'Evita alcohol y cafeína.',
                'Consulta con tu médico sobre tu alimentación.',
            ]),
        };
    }

    private function getSubstitutions(string $level): array
    {
        $substitutions = [
            ['original' => 'Sal de mesa',          'sustituto' => 'Especias naturales (orégano, cúrcuma, ajo)'],
            ['original' => 'Pan blanco',            'sustituto' => 'Pan integral o de centeno'],
            ['original' => 'Arroz blanco',          'sustituto' => 'Arroz integral o quinoa'],
            ['original' => 'Refrescos azucarados',  'sustituto' => 'Agua natural o infusiones sin azúcar'],
            ['original' => 'Embutidos',             'sustituto' => 'Pechuga de pollo o pavo natural'],
        ];

        if ($level !== 'controlada') {
            $substitutions = array_merge($substitutions, [
                ['original' => 'Sopas instantáneas', 'sustituto' => 'Sopas caseras con bajo sodio'],
                ['original' => 'Mantequilla',        'sustituto' => 'Aceite de oliva'],
                ['original' => 'Queso curado',       'sustituto' => 'Queso fresco bajo en sal'],
                ['original' => 'Snacks salados',     'sustituto' => 'Frutos secos sin sal'],
            ]);
        }

        return $substitutions;
    }

    private function getSuggestedFoods(string $level): mixed
    {
        $query = Food::where('sodium_level', 'bajo');

        if ($level !== 'controlada') {
            $query->where('potassium_level', 'alto');
        }

        return $query->orderBy('name')->get(['id', 'name', 'category', 'sodium_level', 'potassium_level']);
    }

    private function getAvoidFoods(string $level): mixed
    {
        $query = Food::where('sodium_level', 'alto');

        return $query->orderBy('name')->get(['id', 'name', 'category', 'sodium_level', 'potassium_level']);
    }

    private function getDashTips(string $level): array
    {
        $tips = [
            'El modelo DASH prioriza frutas, verduras, granos integrales y proteínas magras.',
            'Limita las grasas saturadas y los azúcares simples.',
        ];

        if ($level === 'elevada' || $level === 'alta') {
            $tips[] = 'El sodio es tu nutriente crítico. Lee siempre las etiquetas nutricionales.';
            $tips[] = 'Cocina en casa para controlar mejor la cantidad de sal.';
        }

        if ($level === 'alta') {
            $tips[] = 'Considera llevar un diario de sodio para no exceder 1,500 mg/día.';
        }

        return $tips;
    }
}
