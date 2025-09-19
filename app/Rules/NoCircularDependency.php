<?php

namespace App\Rules;

use App\Models\TaskDependency;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NoCircularDependency implements ValidationRule
{
    protected $taskId;

    public function __construct($taskId)
    {
        $this->taskId = $taskId;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->wouldCreateCircularDependency($this->taskId, $value)) {
            $fail('This would create a circular dependency.');
        }
    }

    /**
     * Check if creating a dependency would create a circular dependency
     */
    private function wouldCreateCircularDependency($taskId, $dependsOnTaskId): bool
    {
        // If the task we're depending on already depends on our task, it's circular
        $circularCheck = TaskDependency::where('task_id', $dependsOnTaskId)
            ->where('depends_on_task_id', $taskId)
            ->exists();

        if ($circularCheck) {
            return true;
        }

        // Check deeper: if any task that depends on our task also depends on the task we want to depend on
        $tasksThatDependOnOurTask = TaskDependency::where('depends_on_task_id', $taskId)
            ->pluck('task_id');

        foreach ($tasksThatDependOnOurTask as $dependentTaskId) {
            if ($this->wouldCreateCircularDependency($dependentTaskId, $dependsOnTaskId)) {
                return true;
            }
        }

        return false;
    }
}
