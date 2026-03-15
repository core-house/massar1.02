<?php

namespace Modules\CRM\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\CRM\Http\Requests\TaskTypeRequest;
use Modules\CRM\Models\TaskType;
use RealRashid\SweetAlert\Facades\Alert;

class TaskTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view Task Types')->only(['index']);
        $this->middleware('can:create Task Types')->only(['create', 'store']);
        $this->middleware('can:edit Task Types')->only(['edit', 'update']);
        $this->middleware('can:delete Task Types')->only(['destroy']);
    }

    public function index(\Illuminate\Http\Request $request)
    {
        $query = TaskType::query();

        // Sorting
        $sortField = $request->get('sort', 'id');
        $sortDirection = $request->get('direction', 'asc');
        
        // Validate sort field to prevent SQL injection
        $allowedSortFields = ['id', 'title', 'description', 'created_at', 'updated_at'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'id';
        }
        
        // Validate sort direction
        $sortDirection = in_array($sortDirection, ['asc', 'desc']) ? $sortDirection : 'asc';
        
        $query->orderBy($sortField, $sortDirection);

        $taskType = $query->paginate(20)->withQueryString();

        return view('crm::task-types.index', compact('taskType', 'sortField', 'sortDirection'));
    }

    public function create()
    {
        return view('crm::task-types.create');
    }

    public function store(TaskTypeRequest $request)
    {
        TaskType::create($request->validated());
        Alert::toast(__('crm::crm.task_type_created_successfully'), 'success');

        return redirect()->route('tasks.types.index');
    }

    public function edit($id)
    {
        $taskType = TaskType::findOrFail($id);

        return view('crm::task-types.edit', compact('taskType'));
    }

    public function update(TaskTypeRequest $request, $id)
    {
        $taskType = TaskType::findOrFail($id);
        $taskType->update($request->validated());
        Alert::toast(__('crm::crm.task_type_updated_successfully'), 'success');

        return redirect()->route('tasks.types.index');
    }

    public function show($id)
    {
        $taskType = TaskType::findOrFail($id);

        return view('crm::task-types.show', compact('taskType'));
    }

    public function destroy($id)
    {
        try {
            $taskType = TaskType::findOrFail($id);
            $taskType->delete();
            Alert::toast(__('crm::crm.task_type_deleted_successfully'), 'success');
        } catch (\Exception) {
            Alert::toast(__('crm::crm.error_deleting_task_type'), 'error');
        }

        return redirect()->route('tasks.types.index');
    }
}
