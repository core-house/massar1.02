<?php

namespace Modules\Inquiries\Http\Controllers;

use Illuminate\Routing\Controller;
use RealRashid\SweetAlert\Facades\Alert;
use Modules\Inquiries\Models\InquiryDocument;
use Modules\Inquiries\Http\Requests\InquiryDocumentRequest;

class InquiryDocumentController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('can:عرض المستندات')->only(['index']);
    //     $this->middleware('can:إضافة المستندات')->only(['create', 'store']);
    //     $this->middleware('can:تعديل المستندات')->only(['edit', 'update']);
    //     $this->middleware('can:حذف المستندات')->only(['destroy']);
    // }

    public function index()
    {
        $documents = InquiryDocument::latest()->get();
        return view('inquiries::project-documents.index', compact('documents'));
    }

    public function create()
    {
        return view('inquiries::project-documents.create');
    }

    public function store(InquiryDocumentRequest $request)
    {
        InquiryDocument::create($request->validated());
        Alert::toast('تم إنشاء المستند بنجاح', 'success');
        return redirect()->route('inquiry.documents.index');
    }

    public function edit($id)
    {
        $document = InquiryDocument::findOrFail($id);
        return view('inquiries::project-documents.edit', compact('document'));
    }

    public function update(InquiryDocumentRequest $request, $id)
    {
        $document = InquiryDocument::findOrFail($id);
        $document->update($request->validated());
        Alert::toast('تم تحديث المستند بنجاح', 'success');
        return redirect()->route('inquiry.documents.index');
    }

    public function destroy($id)
    {
        $document = InquiryDocument::findOrFail($id);
        $document->delete();
        Alert::toast('تم حذف المستند بنجاح', 'success');
        return redirect()->route('inquiry.documents.index');
    }
}
