<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function show(Request $request, $id)
    {
        $user = $request->user();

        $task = Task::with('priority', 'tags', 'attachments')->find($id);

        if (! $task) {
            return response()->json(['error' => 'Task not found.'], 404);
        }

        if (! $user || $task->user->id != $user->id) {
            return response()->json([
                'error' => 'You are not authorized to access this task.',
            ], 401);
        }

        return response()->json([
            'message' => 'Success',
            'data' => $task,
        ], 200);
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $page = $request->page ?? 1;
        $perPage = 10;
        $sort = $request->sort ?? 'created_at';
        $order = $request->order ?? 'asc';

        $completedDateStart = $request->completed_date_start ? Carbon::parse($request->completed_date_start)->startOfDay() : null;
        $completedDateEnd = $request->completed_date_end ? Carbon::parse($request->completed_date_end)->endOfDay() : null;
        $priority = $request->priority;
        $dueDateStart = $request->due_date_start ? Carbon::parse($request->due_date_start)->startOfDay() : null;
        $dueDateEnd = $request->due_date_end ? Carbon::parse($request->due_date_end)->endOfDay() : null;
        $archivedDateStart = $request->archived_date_start ? Carbon::parse($request->archived_date_start)->startOfDay() : null;
        $archivedDateEnd = $request->archived_date_end ? Carbon::parse($request->archived_date_end)->endOfDay() : null;
        $search = $request->search;

        $tasks = $user->tasks()
            ->leftJoin('task_priorities', 'tasks.priority_id', '=', 'task_priorities.id')
            ->select('tasks.*', 'task_priorities.name as priority_name')
            ->when($completedDateStart && $completedDateEnd, function ($query) use ($completedDateStart, $completedDateEnd) {
                $query->whereBetween('tasks.completed_at', [$completedDateStart, $completedDateEnd]);
            })
            ->when($priority, function ($query) use ($priority) {
                $query->where('task_priorities.name', $priority);
            })
            ->when($dueDateStart && $dueDateEnd, function ($query) use ($dueDateStart, $dueDateEnd) {
                $query->whereBetween('tasks.due_date', [$dueDateStart, $dueDateEnd]);
            })
            ->when($archivedDateStart && $archivedDateEnd, function ($query) use ($archivedDateStart, $archivedDateEnd) {
                $query->whereBetween('tasks.archived_at', [$archivedDateStart, $archivedDateEnd]);
            })
            ->when($search, function ($query) use ($search) {
                $query->where('tasks.title', 'like', '%'.$search.'%')
                    ->orWhere('tasks.description', 'like', '%'.$search.'%');
            })
            ->orderBy($sort, $order)
            ->with('tags', 'attachments')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'message' => 'Success',
            'data' => $tasks->items(),
            'meta' => [
                'total' => $tasks->total(),
                'current_page' => $tasks->currentPage(),
                'last_page' => $tasks->lastPage(),
            ],
        ], 200);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'error' => 'Unauthenticated',
                'message' => 'You are not authorized to access this resource.',
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

    public function update(Request $request, $id)
    {
        $user = $request->user();

        $task = Task::with('priority')->find($id);

        if (! $task) {
            return response()->json(['error' => 'Task not found.'], 404);
        }

        if (! $user || $task->user->id != $user->id) {
            return response()->json([
                'error' => 'You are not authorized to edit this task.',
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

        $task->update($request->all());

        return response()->json([
            'message' => 'Success',
            'data' => $task,
        ], 200);
    }

    public function delete(Request $request, $id)
    {
        $user = $request->user();

        $task = Task::find($id);

        if (! $task) {
            return response()->json(['error' => 'Task not found.'], 404);
        }

        if (! $user || $task->user->id != $user->id) {
            return response()->json([
                'error' => 'You are not authorized to delete this task.',
            ], 401);
        }

        $task->delete();

        return response()->json([], 204);
    }

    public function complete(Request $request, $id)
    {
        $user = $request->user();

        $task = Task::with('priority')->find($id);

        if (! $task) {
            return response()->json(['error' => 'Task not found.'], 404);
        }

        if (! $user || $task->user->id != $user->id) {
            return response()->json([
                'error' => 'You are not authorized to update this task.',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'is_completed' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $task->completed_at = $request->is_completed ? now() : null;
        $task->save();

        return response()->json([
            'message' => 'Success',
            'data' => $task,
        ], 200);
    }

    public function updateDueDate(Request $request, $id)
    {
        $user = $request->user();

        $task = Task::with('priority')->find($id);

        if (! $task) {
            return response()->json(['error' => 'Task not found.'], 404);
        }

        if (! $user || $task->user->id != $user->id) {
            return response()->json([
                'error' => 'You are not authorized to update this task.',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'due_date' => 'nullable|date_format:Y-m-d|after_or_equal:today',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $task->due_date = $request->due_date;
        $task->save();

        return response()->json([
            'message' => 'Success',
            'data' => $task,
        ], 200);
    }

    public function archive(Request $request, $id)
    {
        $user = $request->user();

        $task = Task::with('priority')->find($id);

        if (! $task) {
            return response()->json(['error' => 'Task not found.'], 404);
        }

        if (! $user || $task->user->id != $user->id) {
            return response()->json([
                'error' => 'You are not authorized to update this task.',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'is_archived' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $task->archived_at = $request->is_archived ? now() : null;
        $task->save();

        return response()->json([
            'message' => 'Success',
            'data' => $task,
        ], 200);
    }

    public function deleteOldArchivedTasks()
    {
        $oneWeekAgo = Carbon::now()->subWeek();

        Task::where('archived_at', '<=', $oneWeekAgo)->delete();

        return response()->json([
            'message' => 'Old archived tasks have been deleted successfully.',
        ], 200);
    }
}
