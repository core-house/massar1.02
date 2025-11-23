<?php

namespace Modules\Resources\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Resources\Models\ResourceCategory;
use Modules\Resources\Http\Requests\ResourceCategoryRequest;

class ResourceCategoryController extends Controller
{
    public function index()
    {
        $categories = ResourceCategory::ordered()->get();

        return view('resources::categories.index', compact('categories'));
    }

    public function create()
    {
        return view('resources::categories.create');
    }

    public function store(ResourceCategoryRequest $request)
    {
        ResourceCategory::create($request->validated());

        return redirect()
            ->route('resources.categories.index')
            ->with('success', 'تم إضافة التصنيف بنجاح');
    }

    public function edit(ResourceCategory $category)
    {
        return view('resources::categories.edit', compact('category'));
    }

    public function update(ResourceCategoryRequest $request, ResourceCategory $category)
    {
        $category->update($request->validated());

        return redirect()
            ->route('resources.categories.index')
            ->with('success', 'تم تحديث التصنيف بنجاح');
    }

    public function destroy(ResourceCategory $category)
    {
        $category->delete();

        return redirect()
            ->route('resources.categories.index')
            ->with('success', 'تم حذف التصنيف بنجاح');
    }
}

