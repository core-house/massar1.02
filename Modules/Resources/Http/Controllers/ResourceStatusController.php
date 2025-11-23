<?php

namespace Modules\Resources\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Resources\Models\ResourceStatus;
use Modules\Resources\Http\Requests\ResourceStatusRequest;

class ResourceStatusController extends Controller
{
    public function index()
    {
        $statuses = ResourceStatus::ordered()->get();

        return view('resources::statuses.index', compact('statuses'));
    }

    public function create()
    {
        return view('resources::statuses.create');
    }

    public function store(ResourceStatusRequest $request)
    {
        ResourceStatus::create($request->validated());

        return redirect()
            ->route('resources.statuses.index')
            ->with('success', 'تم إضافة الحالة بنجاح');
    }

    public function edit(ResourceStatus $status)
    {
        return view('resources::statuses.edit', compact('status'));
    }

    public function update(ResourceStatusRequest $request, ResourceStatus $status)
    {
        $status->update($request->validated());

        return redirect()
            ->route('resources.statuses.index')
            ->with('success', 'تم تحديث الحالة بنجاح');
    }

    public function destroy(ResourceStatus $status)
    {
        $status->delete();

        return redirect()
            ->route('resources.statuses.index')
            ->with('success', 'تم حذف الحالة بنجاح');
    }
}

