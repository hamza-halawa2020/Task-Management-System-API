<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        $taskId = $this->route('task');
        
        if (!$user) {
            return false;
        }

        // Managers can update any task
        if ($user->isManager()) {
            return true;
        }

        // Users can only update tasks assigned to them
        if ($user->isUser()) {
            $task = \App\Models\Task::find($taskId);
            return $task && $task->assigned_to === $user->id;
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be one of: pending, in_progress, completed, cancelled.',
        ];
    }
}
