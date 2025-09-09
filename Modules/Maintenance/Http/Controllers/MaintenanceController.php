<?php

namespace Modules\Maintenance\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\OperHead;
use RealRashid\SweetAlert\Facades\Alert;
use Modules\Maintenance\Models\Maintenance;
use Modules\Maintenance\Http\Requests\MaintenanceRequest;
use Modules\Maintenance\Models\ServiceType;

class MaintenanceController extends Controller
{
    public function index()
    {
        $maintenances = Maintenance::with('type')->orderBy('accural_date', 'asc')->paginate(20);
        return view('maintenance::maintenances.index', compact('maintenances'));
    }

    public function create()
    {
        $types = ServiceType::all();
        return view('maintenance::maintenances.create', compact('types'));
    }

    public function store(MaintenanceRequest $request)
    {
        try {
            $maintenance = Maintenance::create($request->validated());

            OperHead::create([
                'pro_date' => $request->date,
                'accural_date' => $request->accural_date,
                'info' => 'صيانة ' . $request->item . ' رقم البند ' . $request->item_number,
                'status' => $request->status,
                'op2' => $maintenance->id,
            ]);

            Alert::toast('تم إضافة الصيانة بنجاح', 'success');
            return redirect()->route('maintenances.index');
        } catch (\Exception) {
            Alert::toast('حدث خطأ أثناء إضافة الصيانة: ', 'error');
            return redirect()->back();
        }
    }

    public function edit(Maintenance $maintenance)
    {
        $types = ServiceType::all();
        return view('maintenance::maintenances.edit', compact('maintenance', 'types'));
    }

    public function update(MaintenanceRequest $request, Maintenance $maintenance)
    {
        try {
            $maintenance->update($request->validated());
            $maintenance->operHead()->update([
                'pro_date'      => $request->date,
                'accural_date'  => $request->accural_date,
                'info'          => 'صيانة ' . $request->item_name . ' رقم البند ' . $request->item_number,
                'status'        => $request->status,
            ]);
            Alert::toast('تم تعديل بيانات الصيانة بنجاح', 'success');
            return redirect()->route('maintenances.index');
        } catch (\Exception $e) {
            Alert::toast('حدث خطأ أثناء تعديل بيانات الصيانة: ' . $e->getMessage(), 'error');
            return redirect()->back();
        }
    }

    public function destroy(Maintenance $maintenance)
    {
        try {
            $maintenance->delete();
            Alert::toast('تم حذف الصيانة بنجاح', 'success');
            return redirect()->route('maintenances.index');
        } catch (\Exception) {
            Alert::toast('حدث خطأ أثناء حذف الصيانة: ', 'error');
            return redirect()->back();
        }
    }
}
