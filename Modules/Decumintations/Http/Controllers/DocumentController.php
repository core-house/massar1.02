<?php

declare(strict_types=1);

namespace Modules\Decumintations\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Modules\Decumintations\Http\Requests\DocumentRequest;
use Modules\Decumintations\Models\Document;
use Modules\Decumintations\Models\DocumentCategory;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()->can('view Documents'), 403);

        $documents = Document::with(['category', 'uploadedBy'])
            ->latest()
            ->paginate(20);

        $categories = DocumentCategory::all();

        return view('decumintations::documents.index', compact('documents', 'categories'));
    }

    public function create(): View
    {
        abort_unless(auth()->user()->can('create Documents'), 403);

        $categories = DocumentCategory::all();

        return view('decumintations::documents.create', compact('categories'));
    }

    public function store(DocumentRequest $request): RedirectResponse
    {
        abort_unless(auth()->user()->can('create Documents'), 403);

        $data = $request->validated();

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('documents', 'public');

            $data['file_path']  = $path;
            $data['file_name']  = $file->getClientOriginalName();
            $data['file_type']  = $file->getClientMimeType();
            $data['file_size']  = $file->getSize();
        }

        $data['uploaded_by'] = auth()->id();

        Document::create($data);

        return redirect()->route('documents.index')
            ->with('success', __('decumintations.document_created'));
    }

    public function show(Document $document): View
    {
        abort_unless(auth()->user()->can('view Documents'), 403);

        $document->load(['category', 'uploadedBy']);

        return view('decumintations::documents.show', compact('document'));
    }

    public function edit(Document $document): View
    {
        abort_unless(auth()->user()->can('edit Documents'), 403);

        $categories = DocumentCategory::all();

        return view('decumintations::documents.edit', compact('document', 'categories'));
    }

    public function update(DocumentRequest $request, Document $document): RedirectResponse
    {
        abort_unless(auth()->user()->can('edit Documents'), 403);

        $data = $request->validated();

        if ($request->hasFile('file')) {
            // حذف الملف القديم
            Storage::disk('public')->delete($document->file_path);

            $file = $request->file('file');
            $path = $file->store('documents', 'public');

            $data['file_path']  = $path;
            $data['file_name']  = $file->getClientOriginalName();
            $data['file_type']  = $file->getClientMimeType();
            $data['file_size']  = $file->getSize();
        }

        $document->update($data);

        return redirect()->route('documents.index')
            ->with('success', __('decumintations.document_updated'));
    }

    public function destroy(Document $document): RedirectResponse
    {
        abort_unless(auth()->user()->can('delete Documents'), 403);

        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return redirect()->route('documents.index')
            ->with('success', __('decumintations.document_deleted'));
    }

    public function download(Document $document): StreamedResponse
    {
        abort_unless(auth()->user()->can('view Documents'), 403);

        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }
}
