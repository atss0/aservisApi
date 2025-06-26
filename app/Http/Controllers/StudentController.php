<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\User;

class StudentController extends Controller
{

    public function index()
    {
        $students = Student::with('parent')->get();
        return response()->json($students);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'parent_id' => 'required|exists:users,id',
            'pickup_lat' => 'nullable|numeric',
            'pickup_lng' => 'nullable|numeric',
            'pickup_time' => 'nullable|date_format:H:i',
            'dropoff_lat' => 'nullable|numeric',
            'dropoff_lng' => 'nullable|numeric',
            'dropoff_time' => 'nullable|date_format:H:i',
            'grade' => 'nullable|string',
            'school' => 'nullable|string',
        ]);

        // Kullanıcı gerçekten veli mi?
        $parent = User::where('id', $request->parent_id)
            ->where('role', 'parent')
            ->first();

        if (!$parent) {
            return response()->json(['message' => 'parent_id must belong to a user with role parent'], 422);
        }

        $student = Student::create($request->all());

        return response()->json(['message' => 'Student created.', 'student' => $student], 201);
    }
}
