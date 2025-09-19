<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Task::with(['assignee.role', 'creator.role', 'dependsOn']);

        // Role-based filtering
        if ($user->isUser()) {
            // Users can only see tasks assigned to them
            $query->where('assigned_to', $user->id);
        }
        // Managers can see all tasks (no additional filtering needed)

        // Apply filters
        if ($request->has('status')) {
            $query->byStatus($request->status);
        }

        if ($request->has('assigned_to')) {
            $query->assignedTo($request->assigned_to);
        }

        if ($request->has('due_date_from') && $request->has('due_date_to')) {
            $query->dueBetween($request->due_date_from, $request->due_date_to);
        }

        $tasks = $query->orderBy('created_at', 'desc')->get();

        return response()->json(['tasks' => $tasks]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'assigned_to' => $request->assigned_to,
            'created_by' => $request->user()->id,
        ]);

        return response()->json(['message' => 'Task created successfully', 'task' => $task->load(['assignee.role', 'creator.role']),], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $user = $request->user();
        $query = Task::with(['assignee.role', 'creator.role', 'dependsOn', 'dependencies.dependsOnTask']);

        // Role-based filtering
        if ($user->isUser()) {
            $query->where('assigned_to', $user->id);
        }

        $task = $query->findOrFail($id);

        return response()->json(['task' => $task]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $user = $request->user();
        $task = Task::findOrFail($id);

        // Check permissions and use appropriate request class
        if ($user->isUser()) {
            // Users can only update status of tasks assigned to them
            if ($task->assigned_to !== $user->id) {
                return response()->json(['message' => 'Unauthorized. You can only update tasks assigned to you.'], 403);
            }

            // Check if task can be completed
            if ($request->status === 'completed' && !$task->canBeCompleted()) {
                return response()->json(['message' => 'Cannot complete task. All dependencies must be completed first.',], 422);
            }

            $task->update(['status' => $request->status]);
        } else {

            // Check if task can be completed
            if ($request->has('status') && $request->status === 'completed' && !$task->canBeCompleted()) {
                return response()->json(['message' => 'Cannot complete task. All dependencies must be completed first.'], 422);
            }

            $task->update($request->only(['title', 'description', 'status', 'due_date', 'assigned_to']));
        }

        return response()->json(['message' => 'Task updated successfully', 'task' => $task->load(['assignee.role', 'creator.role', 'dependsOn'])]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        // Only managers can delete tasks
        if (!$request->user()->isManager()) {
            return response()->json(['message' => 'Unauthorized. Only managers can delete tasks.'], 403);
        }

        $task = Task::findOrFail($id);
        $task->delete();

        return response()->json(['message' => 'Task deleted successfully']);
    }
}
