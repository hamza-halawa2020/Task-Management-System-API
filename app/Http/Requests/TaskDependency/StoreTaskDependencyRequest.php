<?php

namespace App\Http\Requests\TaskDependency;

use App\Rules\NoCircularDependency;
use Illuminate\Foundation\Http\FormRequest;

class StoreTaskDependencyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isManager();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'task_id' => 'required|exists:tasks,id',
            'depends_on_task_id' => [
                'required',
                'exists:tasks,id',
                'different:task_id',
                new NoCircularDependency($this->input('task_id'))
            ],
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
            'task_id.required' => 'Task ID is required.',
            'task_id.exists' => 'The selected task does not exist.',
            'depends_on_task_id.required' => 'Dependency task ID is required.',
            'depends_on_task_id.exists' => 'The selected dependency task does not exist.',
            'depends_on_task_id.different' => 'A task cannot depend on itself.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'task_id' => 'task',
            'depends_on_task_id' => 'dependency task',
        ];
    }

}
