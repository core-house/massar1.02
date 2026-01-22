<?php

declare(strict_types=1);

namespace Modules\Tenancy\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Tenancy\Http\Requests\PlanRequest;
use Modules\Tenancy\Models\Plan;
use RealRashid\SweetAlert\Facades\Alert;

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
            Plan::create($request->validated());
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
