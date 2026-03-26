<?php

namespace Modules\Manufacturing\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Manufacturing\Http\Requests\ManufacturingStageRequest;
use Modules\Manufacturing\Models\ManufacturingStage;
use RealRashid\SweetAlert\Facades\Alert;

class ManufacturingStageController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view Manufacturing Stages')->only(['index', 'show']);
        $this->middleware('permission:create Manufacturing Stages')->only(['create', 'store']);
        $this->middleware('permission:edit Manufacturing Stages')->only(['edit', 'update']);
        $this->middleware('permission:delete Manufacturing Stages')->only(['destroy']);
    }

    public function index()
    {
        $stages = ManufacturingStage::paginate(20);

        return view('manufacturing::manufacturing-stages.index', compact('stages'));
    }

    public function create()
    {
        $branches = userBranches();

        return view('manufacturing::manufacturing-stages.create', compact('branches'));
    }

    public function store(ManufacturingStageRequest $request)
    {
        try {
            ManufacturingStage::create($request->validated());
            Alert::toast(__('manufacturing::manufacturing.added_successfully'), 'success');

            return redirect()->route('manufacturing.stages.index');
        } catch (\Exception $e) {
            Alert::toast(__('manufacturing::manufacturing.error_occurred'), 'error');

            return redirect()->back();
        }
    }

    public function show(ManufacturingStage $manufacturingStage)
    {
        $manufacturingStage->load('branch');

        return view('manufacturing::manufacturing-stages.show', compact('manufacturingStage'));
    }

    public function edit(ManufacturingStage $manufacturingStage)
    {
        return view('manufacturing::manufacturing-stages.edit', compact('manufacturingStage'));
    }

    public function update(ManufacturingStageRequest $request, ManufacturingStage $manufacturingStage)
    {
        try {
            $manufacturingStage->update($request->validated());
            Alert::toast(__('manufacturing::manufacturing.updated_successfully'), 'success');

            return redirect()->route('manufacturing.stages.index');
        } catch (\Exception) {
            Alert::toast(__('manufacturing::manufacturing.error_occurred'), 'error');

            return redirect()->back();
        }
    }

    public function destroy(ManufacturingStage $manufacturingStage)
    {
        try {
            $manufacturingStage->delete();
            Alert::toast(__('manufacturing::manufacturing.stage_deleted_successfully'), 'success');

            return redirect()->route('manufacturing.stages.index');
        } catch (\Exception) {
            Alert::toast(__('manufacturing::manufacturing.error_occurred'), 'error');

            return redirect()->back();
        }
    }
}
