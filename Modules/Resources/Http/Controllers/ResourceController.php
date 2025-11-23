<?php

namespace Modules\Resources\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Resources\Models\Resource;
use Modules\Resources\Models\ResourceCategory;
use Modules\Resources\Models\ResourceType;
use Modules\Resources\Models\ResourceStatus;
use Modules\Resources\Http\Requests\ResourceRequest;

class ResourceController extends Controller
{
    public function index()
    {
        return view('resources::index');
    }

    public function create()
    {
        $categories = ResourceCategory::active()->ordered()->get();
        $statuses = ResourceStatus::active()->ordered()->get();

        return view('resources::create', compact('categories', 'statuses'));
    }

    public function store(ResourceRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();

        $resource = Resource::create($data);

        return redirect()
            ->route('resources.index')
            ->with('success', 'تم إضافة المورد بنجاح');
    }

    public function show(Resource $resource)
    {
        $resource->load([
            'category',
            'type',
            'status',
            'branch',
            'employee',
            'assignments.project',
            'statusHistory.oldStatus',
            'statusHistory.newStatus',
            'statusHistory.changedBy',
            'documents',
        ]);

        return view('resources::show', compact('resource'));
    }

    public function edit(Resource $resource)
    {
        $categories = ResourceCategory::active()->ordered()->get();
        $types = ResourceType::active()->forCategory($resource->resource_category_id)->get();
        $statuses = ResourceStatus::active()->ordered()->get();

        return view('resources::edit', compact('resource', 'categories', 'types', 'statuses'));
    }

    public function update(ResourceRequest $request, Resource $resource)
    {
        $data = $request->validated();
        $data['updated_by'] = auth()->id();

        // Track status change
        if (isset($data['resource_status_id']) && $data['resource_status_id'] != $resource->resource_status_id) {
            $resource->statusHistory()->create([
                'old_status_id' => $resource->resource_status_id,
                'new_status_id' => $data['resource_status_id'],
                'changed_by' => auth()->id(),
                'reason' => 'تحديث المورد',
            ]);
        }

        $resource->update($data);

        return redirect()
            ->route('resources.index')
            ->with('success', 'تم تحديث المورد بنجاح');
    }

    public function destroy(Resource $resource)
    {
        $resource->delete();

        return redirect()
            ->route('resources.index')
            ->with('success', 'تم حذف المورد بنجاح');
    }

    public function getTypesByCategory(Request $request)
    {
        $types = ResourceType::active()
            ->forCategory($request->category_id)
            ->get(['id', 'name', 'name_ar']);

        return response()->json($types);
    }
}

