<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskTagController extends Controller
{
    public function store(Request $request, $taskId)
    {
        $user = $request->user();
        $task = Task::find($taskId);

        if (! $task) {
            return response()->json(['error' => 'Task not found.'], 404);
        }

        if (! $user || $task->user->id != $user->id) {
            return response()->json([
                'error' => 'You are not authorized to access this task.',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'tags' => 'required|array',
            'tags.*' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        foreach ($request->tags as $tag) {
            TaskTag::create([
                'task_id' => $task->id,
                'name' => $tag,
            ]);
        }

        return response()->json([
            'message' => 'Success',
            'data' => $task->load('tags'),
        ], 200);
    }

    public function update(Request $request, $taskId)
    {
        $user = $request->user();
        $task = Task::find($taskId);

        if (! $task) {
            return response()->json(['error' => 'Task not found.'], 404);
        }

        if (! $user || $task->user->id != $user->id) {
            return response()->json([
                'error' => 'You are not authorized to access this task.',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'tags' => 'nullable|array',
            'tags.*' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        TaskTag::where('task_id', $task->id)->delete();

        if ($request->has('tags') && count($request->tags) > 0) {
            foreach ($request->tags as $tag) {
                TaskTag::create([
                    'task_id' => $task->id,
                    'name' => $tag,
                ]);
            }
        }

        return response()->json([
            'message' => 'Success',
            'data' => $task->load('tags'),
        ], 200);
    }
}
