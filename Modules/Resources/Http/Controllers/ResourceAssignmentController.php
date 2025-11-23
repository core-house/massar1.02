<?php

namespace Modules\Resources\Http\Controllers;

use App\Models\Project;
use Illuminate\Routing\Controller;
use Modules\Resources\Models\Resource;
use Modules\Resources\Models\ResourceAssignment;
use Modules\Resources\Http\Requests\ResourceAssignmentRequest;

class ResourceAssignmentController extends Controller
{
    public function index()
    {
        $assignments = ResourceAssignment::with(['resource', 'project', 'assignedBy'])
            ->latest()
            ->get();

        return view('resources::assignments.index', compact('assignments'));
    }

    public function create()
    {
        $resources = Resource::active()->with(['category', 'type', 'status'])->get();
        $projects = Project::where('status', '!=', 'completed')->get();

        return view('resources::assignments.create', compact('resources', 'projects'));
    }

    public function store(ResourceAssignmentRequest $request)
    {
        $data = $request->validated();
        $data['assigned_by'] = auth()->id();

        ResourceAssignment::create($data);

        return redirect()
            ->route('resources.assignments.index')
            ->with('success', 'تم تعيين المورد للمشروع بنجاح');
    }

    public function edit(ResourceAssignment $assignment)
    {
        $resources = Resource::active()->with(['category', 'type', 'status'])->get();
        $projects = Project::where('status', '!=', 'completed')->get();

        return view('resources::assignments.edit', compact('assignment', 'resources', 'projects'));
    }

    public function update(ResourceAssignmentRequest $request, ResourceAssignment $assignment)
    {
        $assignment->update($request->validated());

        return redirect()
            ->route('resources.assignments.index')
            ->with('success', 'تم تحديث التعيين بنجاح');
    }

    public function destroy(ResourceAssignment $assignment)
    {
        $assignment->delete();

        return redirect()
            ->route('resources.assignments.index')
            ->with('success', 'تم حذف التعيين بنجاح');
    }
}

