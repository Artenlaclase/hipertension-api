<?php

namespace App\Http\Controllers;

use App\Models\Infusion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InfusionController extends Controller
{
    /**
     * Catálogo de infusiones con filtro por nivel de precaución.
     *
     * GET /api/infusions?level=safe|caution|avoid&category=herbal|tea|other
     */
    public function index(Request $request): JsonResponse
    {
        $query = Infusion::query();

        if ($request->filled('level')) {
            $query->where('precaution_level', $request->level);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $infusions = $query->orderBy('precaution_level')
            ->orderBy('name')
            ->get()
            ->groupBy('precaution_level');

        return response()->json([
            'data' => $infusions,
            'summary' => [
                'safe'    => Infusion::safe()->count(),
                'caution' => Infusion::caution()->count(),
                'avoid'   => Infusion::avoid()->count(),
            ],
            'disclaimer' => 'Estas recomendaciones son informativas. Consulte a su médico antes de incorporar infusiones a su rutina, especialmente si toma medicamentos antihipertensivos.',
        ]);
    }

    /**
     * Detalle de una infusión.
     */
    public function show(Infusion $infusion): JsonResponse
    {
        return response()->json(['data' => $infusion]);
    }

    /**
     * Crear infusión (admin / uso interno).
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'description'      => 'required|string',
            'benefits'         => 'nullable|string',
            'preparation'      => 'nullable|string',
            'precaution_level' => 'required|in:safe,caution,avoid',
            'precaution_note'  => 'nullable|string',
            'category'         => 'nullable|in:herbal,tea,other',
            'recommended_ml'   => 'nullable|integer|min:50|max:1000',
            'max_daily_cups'   => 'nullable|integer|min:1|max:10',
            'image_url'        => 'nullable|url',
        ]);

        $infusion = Infusion::create($data);

        return response()->json(['data' => $infusion], 201);
    }
}
