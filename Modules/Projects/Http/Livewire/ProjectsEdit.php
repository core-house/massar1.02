<?php

declare(strict_types=1);

namespace Modules\Projects\Http\Livewire;

use Livewire\Component;
use Modules\Projects\Models\Project;
use Illuminate\Support\Facades\Auth;

class ProjectsEdit extends Component
{
    public Project $project;
    public $name;
    public $description;
    public $start_date;
    public $end_date;
    public $budget;
    public $actual_end_date;
    public $status;
    public $priority;

    public function mount(Project $project): void
    {
        $this->project = $project;
        $this->name = $project->name;
        $this->description = $project->description;
        $this->budget = $project->budget;
        $this->start_date = $project->start_date ? $project->start_date->format('Y-m-d') : null;
        $this->end_date = $project->end_date ? $project->end_date->format('Y-m-d') : null;
        $this->actual_end_date = $project->actual_end_date ? $project->actual_end_date->format('Y-m-d') : null;
        $this->status = $project->status;
        $this->priority = $project->priority ?? 'medium';
    }

    public function save()
    {
        $validated = $this->validate([
            'name' => 'required|min:3|max:255|unique:projects,name,' . $this->project->id,
            'description' => 'required|min:10',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'actual_end_date' => 'nullable|date|after_or_equal:start_date',
            'budget' => 'nullable|numeric|min:0',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'priority' => 'required|in:low,medium,high',
        ]);

        if (empty($validated['actual_end_date'])) {
            $validated['actual_end_date'] = null;
        }

        $validated['updated_by'] = Auth::id();

        $this->project->update($validated);

        session()->flash('success', 'تم تحديث المشروع بنجاح');
        return redirect()->route('projects.index');
    }

    public function render()
    {
        return view('projects::livewire.projects-edit');
    }
}
