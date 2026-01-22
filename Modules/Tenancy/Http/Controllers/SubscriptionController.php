<?php

declare(strict_types=1);

namespace Modules\Tenancy\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Tenancy\Http\Requests\SubscriptionRequest;
use Modules\Tenancy\Models\Subscription;
use Modules\Tenancy\Models\Tenant;
use Modules\Tenancy\Models\Plan;
use RealRashid\SweetAlert\Facades\Alert;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = Subscription::with(['tenant', 'plan'])->latest()->paginate(15);
        return view('tenancy::subscriptions.index', compact('subscriptions'));
    }

    public function create()
    {
        $tenants = Tenant::all();
        $plans = Plan::where('status', true)->get();
        return view('tenancy::subscriptions.create', compact('tenants', 'plans'));
    }

    public function store(SubscriptionRequest $request)
    {
        try {
            Subscription::create($request->validated());
            Alert::toast(__('Subscription created successfully'), 'success');
            return redirect()->route('subscriptions.index');
        } catch (\Exception $e) {
            Alert::toast(__('An error occurred while processing the data'), 'error');
            return redirect()->back()->withInput();
        }
    }

    public function edit(Subscription $subscription)
    {
        $tenants = Tenant::all();
        $plans = Plan::where('status', true)->get();
        return view('tenancy::subscriptions.edit', compact('subscription', 'tenants', 'plans'));
    }

    public function update(SubscriptionRequest $request, Subscription $subscription)
    {
        try {
            $subscription->update($request->validated());
            Alert::toast(__('Subscription updated successfully'), 'success');
            return redirect()->route('subscriptions.index');
        } catch (\Exception $e) {
            Alert::toast(__('An error occurred while processing the data'), 'error');
            return redirect()->back()->withInput();
        }
    }

    public function destroy(Subscription $subscription)
    {
        try {
            $subscription->delete();
            Alert::toast(__('Subscription deleted successfully'), 'success');
            return redirect()->route('subscriptions.index');
        } catch (\Exception $e) {
            Alert::toast(__('An error occurred while deleting the subscription'), 'error');
            return redirect()->back();
        }
    }

    public function toggleStatus(Subscription $subscription)
    {
        try {
            $subscription->update(['status' => !$subscription->status]);
            Alert::toast(__('Status updated successfully'), 'success');
            return redirect()->back();
        } catch (\Exception $e) {
            Alert::toast(__('Failed to update status'), 'error');
            return redirect()->back();
        }
    }
}
