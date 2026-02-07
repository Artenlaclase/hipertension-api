<?php

namespace App\Http\Controllers;

use App\Models\Food;
use Illuminate\Http\Request;

class FoodController extends Controller
{
    public function index(Request $request)
    {
        $query = Food::query();

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('sodium_level')) {
            $query->where('sodium_level', $request->sodium_level);
        }

        return response()->json($query->orderBy('name')->get());
    }

    public function show(Food $food)
    {
        return response()->json($food);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'sodium_level'    => 'required|string|in:bajo,medio,alto',
            'potassium_level' => 'required|string|in:bajo,medio,alto',
            'category'        => 'required|string|max:255',
        ]);

        $food = Food::create($request->only([
            'name', 'sodium_level', 'potassium_level', 'category'
        ]));

        return response()->json($food, 201);
    }
}
