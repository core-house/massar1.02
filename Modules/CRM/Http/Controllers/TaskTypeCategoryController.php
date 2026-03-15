<?php

declare(strict_types=1);

namespace Modules\CRM\Http\Controllers;

use Modules\CRM\Models\TaskTypeCategory;
use Modules\CRM\Http\Requests\TaskTypeCategoryRequest;
use App\Http\Controllers\Controller;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class TaskTypeCategoryController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:view Task Types', only: ['index']),
            new Middleware('can:create Task Types', only: ['create', 'store']),
            new Middleware('can:edit Task Types', only: ['edit', 'update']),
            new Middleware('can:delete Task Types', only: ['destroy']),
        ];
    }

    public function index()
    {
        $categories = TaskTypeCategory::with('taskTypes')->get();
        return view('crm::task-type-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('crm::task-type-categories.create');
    }

    public function store(TaskTypeCategoryRequest $request)
    {
        try {
            TaskTypeCategory::create($request->validated());
            Alert::success(__('crm::crm.success'), __('crm::crm.category_created_successfully'));
            return redirect()->route('tasks.type-categories.index');
        } catch (\Exception $e) {
            Alert::error(__('crm::crm.error'), __('crm::crm.failed_to_create_category'));
            return redirect()->back()->withInput();
        }
    }

    public function edit(TaskTypeCategory $taskTypeCategory)
    {
        return view('crm::task-type-categories.edit', ['category' => $taskTypeCategory]);
    }

    public function update(TaskTypeCategoryRequest $request, TaskTypeCategory $taskTypeCategory)
    {
        try {
            $taskTypeCategory->update($request->validated());
            Alert::success(__('crm::crm.success'), __('crm::crm.category_updated_successfully'));
            return redirect()->route('tasks.type-categories.index');
        } catch (\Exception $e) {
            Alert::error(__('crm::crm.error'), __('crm::crm.failed_to_update_category'));
            return redirect()->back()->withInput();
        }
    }

    public function destroy(TaskTypeCategory $taskTypeCategory)
    {
        try {
            $taskTypeCategory->delete();
            Alert::success(__('crm::crm.success'), __('crm::crm.category_deleted_successfully'));
            return redirect()->route('tasks.type-categories.index');
        } catch (\Exception $e) {
            Alert::error(__('crm::crm.error'), __('crm::crm.failed_to_delete_category'));
            return redirect()->back();
        }
    }
}
