<?php

declare(strict_types=1);

namespace Modules\Tenancy\Http\Controllers;

use Modules\Tenancy\Models\Plan;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Modules\Tenancy\Http\Requests\PlanRequest;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::latest()->paginate(15);
        return view('tenancy::plans.index', compact('plans'));
    }

    public function create()
    {
        return view('tenancy::plans.create');
    }

    public function store(PlanRequest $request)
    {
        try {
            $data = $request->validated();
            $data['created_by'] = Auth::user()->name;
            Plan::create($data);
            Alert::toast(__('Plan created successfully'), 'success');
            return redirect()->route('plans.index');
        } catch (\Exception $e) {
            Alert::toast(__('An error occurred while processing the data'), 'error');
            return redirect()->back()->withInput();
        }
    }

    public function edit(Plan $plan)
    {
        return view('tenancy::plans.edit', compact('plan'));
    }

    public function update(PlanRequest $request, Plan $plan)
    {
        try {
            $plan->update($request->validated());
            Alert::toast(__('Plan updated successfully'), 'success');
            return redirect()->route('plans.index');
        } catch (\Exception $e) {
            Alert::toast(__('An error occurred while processing the data'), 'error');
            return redirect()->back()->withInput();
        }
    }

    public function destroy(Plan $plan)
    {
        try {
            $plan->delete();
            Alert::toast(__('Plan deleted successfully'), 'success');
            return redirect()->route('plans.index');
        } catch (\Exception $e) {
            Alert::toast(__('An error occurred while deleting the plan'), 'error');
            return redirect()->back();
        }
    }

    public function toggleStatus(Plan $plan)
    {
        try {
            $plan->update(['status' => !$plan->status]);
            Alert::toast(__('Status updated successfully'), 'success');
            return redirect()->back();
        } catch (\Exception $e) {
            Alert::toast(__('Failed to update status'), 'error');
            return redirect()->back();
        }
    }
}
