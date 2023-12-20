<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TaskAttachmentController extends Controller
{
    public function store(Request $request, $id)
    {
        $user = $request->user();
        $task = Task::find($id);

        if (!$task) {
            return response()->json(['error' => 'Task not found.'], 404);
        }

        if (!$user || $task->user->id != $user->id) {
            return response()->json([
                'error' => 'You are not authorized to attach files to this task.',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'attachments' => 'required|array',
            'attachments.*' => 'required|mimes:svg,png,jpg,mp4,csv,txt,doc,docx'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        if ($request->has('attachments') && count($request->attachments) > 0) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('public');
                $name = $file->getClientOriginalName();

                TaskAttachment::create([
                    'task_id' => $task->id,
                    'name' => $name,
                    'path' => Storage::url($path),
                ]);
            }
        }

        return response()->json([
            'message' => 'Files have been attached successfully.',
            'data' => $task->load('attachments'),
        ], 200);
    }
}
