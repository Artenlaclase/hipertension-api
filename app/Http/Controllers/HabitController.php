<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use Illuminate\Http\Request;

class HabitController extends Controller
{
    public function index()
    {
        return response()->json(Habit::orderBy('name')->get());
    }

    public function show(Habit $habit)
    {
        return response()->json($habit);
    }
}
