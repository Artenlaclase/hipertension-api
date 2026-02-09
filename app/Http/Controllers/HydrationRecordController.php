<?php

namespace App\Http\Controllers;

use App\Models\HydrationRecord;
use App\Http\Resources\HydrationRecordResource;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HydrationRecordController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->user()->hydrationRecords()->orderByDesc('recorded_at');

        if ($request->has('date')) {
            $date = Carbon::parse($request->date);
            $query->whereDate('recorded_at', $date);
        }

        $records = $query->get();
        $goal = $request->user()->hydrationGoals()
            ->where('effective_date', '<=', now()->toDateString())
            ->orderByDesc('effective_date')
            ->first();

        return response()->json([
            'data' => HydrationRecordResource::collection($records),
            'meta' => [
                'total_ml' => $records->sum('amount_ml'),
                'goal_ml' => $goal?->goal_ml ?? 2000,
                'progress' => $records->sum('amount_ml') / ($goal?->goal_ml ?? 2000),
                'records_count' => $records->count(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'liquid_type' => 'required|in:water,infusion,juice,broth,other',
            'amount_ml' => 'required|integer|min:1|max:5000',
            'note' => 'nullable|string|max:500',
            'recorded_at' => 'required|date',
        ]);

        $record = $request->user()->hydrationRecords()->create($validated);

        return new HydrationRecordResource($record);
    }

    public function show(HydrationRecord $hydrationRecord)
    {
        $this->authorize('view', $hydrationRecord);
        return new HydrationRecordResource($hydrationRecord);
    }

    public function update(Request $request, HydrationRecord $hydrationRecord)
    {
        $this->authorize('update', $hydrationRecord);

        $validated = $request->validate([
            'liquid_type' => 'in:water,infusion,juice,broth,other',
            'amount_ml' => 'integer|min:1|max:5000',
            'note' => 'nullable|string|max:500',
            'recorded_at' => 'date',
        ]);

        $hydrationRecord->update($validated);
        return new HydrationRecordResource($hydrationRecord);
    }

    public function destroy(HydrationRecord $hydrationRecord)
    {
        $this->authorize('delete', $hydrationRecord);
        $hydrationRecord->delete();
        return response()->json(null, 204);
    }
}
