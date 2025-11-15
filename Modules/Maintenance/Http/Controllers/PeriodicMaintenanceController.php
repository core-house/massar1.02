<?php

namespace Modules\Maintenance\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Maintenance\Models\PeriodicMaintenanceSchedule;
use Modules\Maintenance\Models\ServiceType;
use Modules\Maintenance\Http\Requests\PeriodicMaintenanceRequest;
use RealRashid\SweetAlert\Facades\Alert;

class PeriodicMaintenanceController extends Controller
{
    public function index()
    {
        $schedules = PeriodicMaintenanceSchedule::with('serviceType')
            ->orderBy('next_maintenance_date', 'asc')
            ->paginate(20);

        return view('maintenance::periodic.index', compact('schedules'));
    }

    public function create()
    {
        $types = ServiceType::all();
        $branches = userBranches();
        return view('maintenance::periodic.create', compact('types', 'branches'));
    }

    public function store(PeriodicMaintenanceRequest $request)
    {
        try {
            PeriodicMaintenanceSchedule::create($request->validated());
            Alert::toast('تم إضافة جدول الصيانة الدورية بنجاح', 'success');
            return redirect()->route('periodic.maintenances.index');
        } catch (\Exception) {
            Alert::toast('حدث خطأ: ', 'error');
            return redirect()->back()->withInput();
        }
    }

    public function edit(PeriodicMaintenanceSchedule $periodicMaintenance)
    {
        $types = ServiceType::all();
        return view('maintenance::periodic.edit', compact('periodicMaintenance', 'types'));
    }

    public function update(PeriodicMaintenanceRequest $request, PeriodicMaintenanceSchedule $periodicMaintenance)
    {
        try {
            $periodicMaintenance->update($request->validated());
            Alert::toast('تم تعديل جدول الصيانة الدورية بنجاح', 'success');
            return redirect()->route('periodic.maintenances.index');
        } catch (\Exception) {
            Alert::toast('حدث خطأ: ', 'error');
            return redirect()->back();
        }
    }

    public function destroy(PeriodicMaintenanceSchedule $periodicMaintenance)
    {
        try {
            $periodicMaintenance->delete();
            Alert::toast('تم حذف جدول الصيانة الدورية بنجاح', 'success');
            return redirect()->route('periodic.maintenances.index');
        } catch (\Exception) {
            Alert::toast('حدث خطأ: ', 'error');
            return redirect()->back();
        }
    }

    /**
     * إنشاء صيانة من جدول دوري
     */
    public function createMaintenanceFromSchedule(PeriodicMaintenanceSchedule $schedule)
    {
        return view('maintenance::maintenances.create-from-schedule', compact('schedule'));
    }

    /**
     * تفعيل/تعطيل الجدول
     */
    public function toggleActive(PeriodicMaintenanceSchedule $periodicMaintenance)
    {
        try {
            $periodicMaintenance->update(['is_active' => !$periodicMaintenance->is_active]);
            $status = $periodicMaintenance->is_active ? 'تفعيل' : 'تعطيل';
            Alert::toast("تم {$status} جدول الصيانة بنجاح", 'success');
            return redirect()->back();
        } catch (\Exception) {
            Alert::toast('حدث خطأ: ', 'error');
            return redirect()->back();
        }
    }
}
