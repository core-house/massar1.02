<?php

declare(strict_types=1);

namespace Modules\Projects\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Projects\Models\Project;
use Illuminate\Support\Str;

class ProjectsIndex extends Component
{
    use WithPagination;

    public function getBudgetStatus($project): array
    {
        $totalPaid = $project->operations()->where('pro_type', 2)->sum('pro_value');
        $totalReceived = $project->operations()->where('pro_type', 1)->sum('pro_value');
        $budget = $totalReceived;
        $spent = $totalPaid;
        
        if ($budget == 0) {
            return ['status' => 'no_budget', 'class' => 'secondary', 'text' => __('no_budget')];
        }
        
        if ($spent > $budget) {
            return ['status' => 'over', 'class' => 'danger', 'text' => __('budget_exceeded')];
        } elseif ($spent == $budget) {
            return ['status' => 'equal', 'class' => 'warning', 'text' => __('budget_equal')];
        } else {
            return ['status' => 'under', 'class' => 'success', 'text' => __('budget_under')];
        }
    }

    public function delete(Project $project): void
    {
        $project->delete();
        session()->flash('success', __('project_deleted_successfully'));
    }

    public function getStatusBadgeClass($status): string
    {
        return match ($status) {
            'pending' => 'bg-warning',
            'in_progress' => 'bg-info',
            'completed' => 'bg-success',
            'cancelled' => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    public function getStatusText($status): string
    {
        return match ($status) {
            'pending' => __('status_pending'),
            'in_progress' => __('status_in_progress'),
            'completed' => __('status_completed'),
            'cancelled' => __('status_cancelled'),
            default => __('unknown'),
        };
    }

    public function getPriorityBadgeClass($priority): string
    {
        return match ($priority) {
            'high' => 'bg-danger',
            'medium' => 'bg-warning',
            'low' => 'bg-success',
            default => 'bg-secondary',
        };
    }

    public function getPriorityText($priority): string
    {
        return match ($priority) {
            'high' => __('priority_high'),
            'medium' => __('priority_medium'),
            'low' => __('priority_low'),
            default => __('priority_medium'),
        };
    }

    public function render()
    {
        $projects = Project::with(['createdBy', 'updatedBy'])->latest()->paginate(10);
        
        return view('projects::livewire.projects-index', [
            'projects' => $projects,
        ]);
    }
}
