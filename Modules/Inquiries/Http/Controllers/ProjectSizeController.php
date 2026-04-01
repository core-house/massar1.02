<?php

namespace Modules\Inquiries\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Inquiries\Http\Requests\ProjectSizeRequest;
use Modules\Inquiries\Models\ProjectSize;
use RealRashid\SweetAlert\Facades\Alert;

class ProjectSizeController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view Project Size')->only('index');
        $this->middleware('can:create Project Size')->only(['create', 'store']);
        $this->middleware('can:edit Project Size')->only(['edit', 'update']);
        $this->middleware('can:delete Project Size')->only('destroy');
    }

    public function index()
    {
        try {
            $projectSizes = ProjectSize::all();

            return view('inquiries::project-size.index', compact('projectSizes'));
        } catch (\Exception) {
            Alert::toast(__('inquiries::inquiries.error_loading_data'), 'error');

            return redirect()->back();
        }
    }

    public function create()
    {
        return view('inquiries::project-size.create');
    }

    public function store(ProjectSizeRequest $request)
    {
        try {
            ProjectSize::create($request->validated());
            Alert::toast(__('inquiries::inquiries.project_size_created_successfully'), 'success');

            return redirect()->route('project-size.index');
        } catch (\Exception $e) {
            Alert::toast(__('inquiries::inquiries.unexpected_error'), 'error');

            return redirect()->back()->withInput();
        }
    }

    public function edit($id)
    {
        try {
            $projectSize = ProjectSize::findOrFail($id);

            return view('inquiries::project-size.edit', compact('projectSize'));
        } catch (\Exception $e) {
            Alert::toast(__('inquiries::inquiries.error_loading_data'), 'error');

            return redirect()->route('project-size.index');
        }
    }

    public function update(ProjectSizeRequest $request, $id)
    {
        try {
            $projectSize = ProjectSize::findOrFail($id);
            $projectSize->update($request->validated());
            Alert::toast(__('inquiries::inquiries.project_size_updated_successfully'), 'success');

            return redirect()->route('project-size.index');
        } catch (\Exception $e) {
            Alert::toast(__('inquiries::inquiries.unexpected_error'), 'error');

            return redirect()->back()->withInput();
        }
    }

    public function show($id)
    {
        $projectSize = ProjectSize::findOrFail($id);

        return view('inquiries::project-size.show', compact('projectSize'));
    }

    public function destroy($id)
    {
        try {
            $projectSize = ProjectSize::findOrFail($id);
            $projectSize->delete();
            Alert::toast(__('inquiries::inquiries.project_size_deleted_successfully'), 'success');

            return redirect()->route('project-size.index');
        } catch (\Exception $e) {
            Alert::toast(__('inquiries::inquiries.unexpected_error'), 'error');

            return redirect()->back();
        }
    }
}
