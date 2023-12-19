<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'error' => 'Unauthenticated',
                'message' => 'You are not authorized to access this resource.'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'due_date' => 'nullable|date_format:Y-m-d|after_or_equal:today',
            'priority_id' => 'nullable|exists:task_priorities,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $task = Task::create([
            'user_id' => $user->id,
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'due_date' => $request->input('due_date'),
            'priority_id' => $request->input('priority_id'),
        ]);

        return response()->json([
            'message' => 'Success',
            'data' => $task,
        ], 200);
    }
}
