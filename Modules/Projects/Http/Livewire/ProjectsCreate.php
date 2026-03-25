<?php

declare(strict_types=1);

namespace Modules\Projects\Http\Livewire;

use Livewire\Component;
use Modules\Projects\Models\Project;
use Illuminate\Support\Facades\Auth;

class ProjectsCreate extends Component
{
    public $name = '';
    public $description = '';
    public $start_date = '';
    public $end_date = '';
    public $budget;
    public $actual_end_date = null;
    public $status = 'pending';
    public $priority = 'medium';

    public function save()
    {
        $validated = $this->validate([
            'name' => 'required|min:3|max:255|unique:projects,name',
            'description' => 'required|min:10',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'actual_end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'priority' => 'required|in:low,medium,high',
            'budget' => 'nullable|numeric|min:0',
        ]);

        if (empty($validated['actual_end_date'])) {
            $validated['actual_end_date'] = null;
        }

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();
        
        Project::create($validated);
        
        session()->flash('success', 'تم إنشاء المشروع بنجاح');
        return redirect()->route('projects.index');
    }

    public function render()
    {
        return view('projects::livewire.projects-create');
    }
}
