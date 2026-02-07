<?php

namespace App\Http\Controllers;

use App\Models\EducationalContent;
use Illuminate\Http\Request;

class EducationalContentController extends Controller
{
    public function index(Request $request)
    {
        $query = EducationalContent::query();

        if ($request->has('topic')) {
            $query->where('topic', $request->topic);
        }

        if ($request->has('level')) {
            $query->where('level', $request->level);
        }

        return response()->json($query->orderBy('created_at', 'desc')->get());
    }

    public function show(EducationalContent $educationalContent)
    {
        return response()->json($educationalContent);
    }
}
