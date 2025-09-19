<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskDependency\StoreTaskDependencyRequest;
use App\Models\Task;
use App\Models\TaskDependency;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TaskDependencyController extends Controller
{
    /**
     * Add a dependency to a task
     */
    public function store(StoreTaskDependencyRequest $request): JsonResponse
    {
        $dependency = TaskDependency::create([
            'task_id' => $request->task_id,
            'depends_on_task_id' => $request->depends_on_task_id,
        ]);

        return response()->json(['message' => 'Dependency added successfully', 'dependency' => $dependency->load(['task.assignee.role', 'dependsOnTask.assignee.role'])], 201);
    }

    /**
     * Remove a dependency from a task
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        // Only managers can remove dependencies
        if (!$request->user()->isManager()) {
            return response()->json(['message' => 'Unauthorized. Only managers can remove task dependencies.'], 403);
        }

        $dependency = TaskDependency::findOrFail($id);
        $dependency->delete();

        return response()->json(['message' => 'Dependency removed successfully']);
    }

    /**
     * Get dependencies for a specific task
     */
    public function show(Request $request, string $taskId): JsonResponse
    {
        $user = $request->user();
        $task = Task::findOrFail($taskId);

        // Check if user has access to this task
        if ($user->isUser() && $task->assigned_to !== $user->id) {
            return response()->json(['message' => 'Unauthorized. You can only view dependencies for tasks assigned to you.'], 403);
        }

        $dependencies = TaskDependency::where('task_id', $taskId)->with(['dependsOnTask'])->get();

        return response()->json(['task' => $task, 'dependencies' => $dependencies]);
    }
}
