<?php

namespace Modules\Manufacturing\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Manufacturing\Models\ManufacturingStage;
use Modules\Manufacturing\Http\Requests\ManufacturingStageRequest;
use RealRashid\SweetAlert\Facades\Alert;

class ManufacturingStageController extends Controller
{

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
            Alert::toast('تم الإنشاء بنجاح', 'success');
            return redirect()->route('manufacturing.stages.index');
        } catch (\Exception) {
            Alert::toast('حدث خطأ عند التسجيل', 'error');
            return redirect()->back();
        }
    }


    public function show(ManufacturingStage $manufacturingStage) {}


    public function edit(ManufacturingStage $manufacturingStage)
    {
        return view('manufacturing::manufacturing-stages.edit', compact('manufacturingStage'));
    }

    public function update(ManufacturingStageRequest $request, ManufacturingStage $manufacturingStage)
    {
        try {
            $manufacturingStage->update($request->validated());
            Alert::toast('تم التعديل بنجاح', 'success');
            return redirect()->route('manufacturing.stages.index');
        } catch (\Exception) {
            Alert::toast('حدث خطأ عند التعديل', 'error');
            return redirect()->back();
        }
    }

    public function destroy(ManufacturingStage $manufacturingStage)
    {
        try {
            $manufacturingStage->delete();
            Alert::toast('تم حذف المرحلة بنجاح', 'success');
            return redirect()->route('manufacturing.stages.index');
        } catch (\Exception) {
            Alert::toast('حدث خطأ أثناء حذف المرحلة', 'error');
            return redirect()->back();
        }
    }
}
