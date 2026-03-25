<?php

declare(strict_types=1);

namespace Modules\Decumintations\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\Decumintations\Http\Requests\DocumentCategoryRequest;
use Modules\Decumintations\Models\DocumentCategory;

class DocumentCategoryController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()->can('view Document Categories'), 403);

        $categories = DocumentCategory::withCount('documents')->latest()->paginate(20);

        return view('decumintations::categories.index', compact('categories'));
    }

    public function create(): View
    {
        abort_unless(auth()->user()->can('create Document Categories'), 403);

        return view('decumintations::categories.create');
    }

    public function store(DocumentCategoryRequest $request): RedirectResponse
    {
        abort_unless(auth()->user()->can('create Document Categories'), 403);

        DocumentCategory::create($request->validated());

        return redirect()->route('document-categories.index')
            ->with('success', __('decumintations.category_created'));
    }

    public function edit(DocumentCategory $documentCategory): View
    {
        abort_unless(auth()->user()->can('edit Document Categories'), 403);

        return view('decumintations::categories.edit', compact('documentCategory'));
    }

    public function update(DocumentCategoryRequest $request, DocumentCategory $documentCategory): RedirectResponse
    {
        abort_unless(auth()->user()->can('edit Document Categories'), 403);

        $documentCategory->update($request->validated());

        return redirect()->route('document-categories.index')
            ->with('success', __('decumintations.category_updated'));
    }

    public function destroy(DocumentCategory $documentCategory): RedirectResponse
    {
        abort_unless(auth()->user()->can('delete Document Categories'), 403);

        $documentCategory->delete();

        return redirect()->route('document-categories.index')
            ->with('success', __('decumintations.category_deleted'));
    }
}
