<?php

namespace Modules\Inquiries\Http\Controllers;

use App\Http\Controllers\Controller;
use RealRashid\SweetAlert\Facades\Alert;
use Modules\Inquiries\Models\ProjectSize;
use Modules\Inquiries\Http\Requests\ProjectSizeRequest;

class ProjectSizeController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('can:عرض حجم المشروع')->only(['index']);
    //     $this->middleware('can:إضافة حجم المشروع')->only(['create', 'store']);
    //     $this->middleware('can:تعديل حجم المشروع')->only(['edit', 'update']);
    //     $this->middleware('can:حذف حجم المشروع')->only(['destroy']);
    // }

    public function index()
    {
        $projectSizes = ProjectSize::all();
        return view('inquiries::project-size.index', compact('projectSizes'));
    }

    public function create()
    {
        return view('inquiries::project-size.create');
    }

    public function store(ProjectSizeRequest $request)
    {
        ProjectSize::create($request->validated());
        Alert::toast('تم الانشاء بنجاح', 'success');
        return redirect()->route('project-size.index');
    }

    public function edit($id)
    {
        $projectSize = ProjectSize::findOrFail($id);
        return view('inquiries::project-size.edit', compact('projectSize'));
    }

    public function update(ProjectSizeRequest $request, $id)
    {
        $projectSize = ProjectSize::findOrFail($id);
        $projectSize->update($request->validated());
        Alert::toast('تم التعديل بنجاح', 'success');
        return redirect()->route('project-size.index');
    }

    public function destroy($id)
    {
        $projectSize = ProjectSize::findOrFail($id);
        $projectSize->delete();
        Alert::toast('تم حذف العنصر بنجاح', 'success');
        return redirect()->route('project-size.index');
    }
}
