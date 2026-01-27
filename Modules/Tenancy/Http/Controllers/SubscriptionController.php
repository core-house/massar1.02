<?php

declare(strict_types=1);

namespace Modules\Tenancy\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Tenancy\Models\Plan;
use Modules\Tenancy\Models\Tenant;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Tenancy\Models\Subscription;
use RealRashid\SweetAlert\Facades\Alert;
use Modules\Tenancy\Http\Requests\SubscriptionRequest;

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
            $data = $request->validated();
            $data['created_by'] = Auth::user()->name;
            Subscription::create($data);

            // تفعيل التينانت عند إضافة اشتراك جديد
            Tenant::find($data['tenant_id'])->update(['status' => true]);

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

    public function renew(Subscription $subscription)
    {
        try {
            $startDate = $subscription->ends_at->isPast() ? now() : $subscription->ends_at;
            $duration = $subscription->starts_at->diffInDays($subscription->ends_at);
            $endDate = (clone $startDate)->addDays((int) $duration);

            Subscription::create([
                'tenant_id' => $subscription->tenant_id,
                'plan_id' => $subscription->plan_id,
                'starts_at' => $startDate,
                'ends_at' => $endDate,
                'paid_amount' => $subscription->paid_amount,
                'status' => true,
                'created_by' => Auth::user()->name,
            ]);

            // تفعيل التينانت عند تجديد الاشتراك
            $subscription->tenant->update(['status' => true]);

            Alert::toast(__('Subscription renewed successfully'), 'success');
            return redirect()->back();
        } catch (\Exception $e) {
            Alert::toast(__('Failed to renew subscription'), 'error');
            return redirect()->back();
        }
    }

    public function renewWithAmount(Request $request, Subscription $subscription)
    {
        $request->validate([
            'paid_amount' => 'required|numeric|min:0',
        ]);

        try {
            $startDate = $subscription->ends_at->isPast() ? now() : $subscription->ends_at;
            $duration = $subscription->starts_at->diffInDays($subscription->ends_at);
            $endDate = (clone $startDate)->addDays((int) $duration);

            Subscription::create([
                'tenant_id' => $subscription->tenant_id,
                'plan_id' => $subscription->plan_id,
                'starts_at' => $startDate,
                'ends_at' => $endDate,
                'paid_amount' => $request->paid_amount,
                'status' => true,
                'created_by' => Auth::user()->name,
            ]);

            // تفعيل التينانت عند تجديد الاشتراك
            $subscription->tenant->update(['status' => true]);

            Alert::toast(__('Subscription renewed with new amount successfully'), 'success');
            return redirect()->back();
        } catch (\Exception $e) {
            Alert::toast(__('Failed to renew subscription'), 'error');
            return redirect()->back();
        }
    }
}
