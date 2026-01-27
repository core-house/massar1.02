<?php

declare(strict_types=1);

namespace Modules\Tenancy\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Tenancy\Http\Requests\TenantRequest;
use Modules\Tenancy\Models\Tenant;
use Modules\Tenancy\Models\Plan;
use Modules\Tenancy\Models\Subscription;
use RealRashid\SweetAlert\Facades\Alert;

class TenancyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tenants = Tenant::with(['domains', 'plan'])
            ->latest()
            ->paginate(15);

        return view('tenancy::tenancies.index', compact('tenants'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $plans = Plan::where('status', true)->get();
        return view('tenancy::tenancies.create', compact('plans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TenantRequest $request)
    {
        try {
            DB::beginTransaction();

        $fullDomain = $this->getFullDomain($request->subdomain);

        // إنشاء التينانت مع كافة الحقول الجديدة
        $tenant = Tenant::create([
            'id' => $request->subdomain,
            'name' => $request->name,
            'domain' => $fullDomain,
            'contact_number' => $request->contact_number,
            'address' => $request->address,
            'company_name' => $request->company_name,
            'company_size' => $request->company_size,
            'admin_email' => $request->admin_email,
            'user_position' => $request->user_position,
            'referral_code' => $request->referral_code,
            'plan_id' => $request->plan_id,
            'status' => $request->status ?? true,
            'enabled_modules' => $request->enabled_modules ?? [],
            'created_by' => Auth::user()->name,
        ]);

        // إنشاء الدومين
        $tenant->domains()->create([
            'domain' => $fullDomain,
        ]);

        // إنشاء اشتراك أولي إذا تم تحديد تواريخ
        if ($request->subscription_start_at && $request->subscription_end_at) {
            Subscription::create([
                'tenant_id' => $tenant->id,
                'plan_id' => $request->plan_id,
                'starts_at' => $request->subscription_start_at,
                'ends_at' => $request->subscription_end_at,
                'paid_amount' => Plan::find($request->plan_id)->amount ?? 0,
                'status' => true,
                'created_by' => Auth::user()->name,
            ]);

            // التأكد من أن التينانت نشط
            $tenant->update(['status' => true]);
        }

        // التحقق من إنشاء قاعدة البيانات وجلب البيانات المولدة تلقائياً
        DB::commit();
        $tenant->refresh();


        Alert::toast(__('Tenant created successfully'), 'success');

        $domain = $tenant->domains->first();
        $tenantUrl = $this->getTenantUrl($domain->domain);

        return redirect($tenantUrl);
        } catch (\Exception $e) {
            DB::rollBack();
            Alert::toast(__('Failed to create tenant: :message', ['message' => $e->getMessage()]), 'error');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $tenant = Tenant::with(['domains', 'plan', 'subscriptions'])->findOrFail($id);

        return view('tenancy::tenancies.show', compact('tenant'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $tenant = Tenant::with('domains')->findOrFail($id);
        $domain = $tenant->domains->first();
        $plans = Plan::where('status', true)->get();

        return view('tenancy::tenancies.edit', compact('tenant', 'domain', 'plans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TenantRequest $request, $id)
    {
        try {
            DB::beginTransaction();

        $tenant = Tenant::findOrFail($id);
        $domainModel = $tenant->domains->first();

        $data = $request->validated();

        $data['enabled_modules'] = $request->enabled_modules ?? [];


        $tenant->update($data);

        // تحديث الدومين إذا تغير السابدومين
        $newDomain = $this->getFullDomain($request->subdomain);
        if ($domainModel && $domainModel->domain !== $newDomain) {
            // ملاحظة: تغيير الـ domain ممكن ولكن تغيير ID التينانت غير مسموح به في هذا التنفيذ
            $domainModel->update([
                'domain' => $newDomain,
            ]);
            $tenant->update(['domain' => $newDomain]);
        }

            DB::commit();

        Alert::toast(__('Tenant updated successfully'), 'success');
        return redirect()->route('tenancy.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Alert::toast(__('Failed to update tenant: :message', ['message' => $e->getMessage()]), 'error');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $tenant = Tenant::findOrFail($id);
            $tenant->delete();
            Alert::toast(__('Tenant deleted successfully'), 'success');
            return redirect()->route('tenancy.index');
        } catch (\Exception $e) {
            Alert::toast(__('Failed to delete tenant: :message', ['message' => $e->getMessage()]), 'error');
            return redirect()->back();
        }
    }

    /**
     * Redirect to tenant subdomain.
     */
    public function redirectToTenant($id)
    {
        $tenant = Tenant::findOrFail($id);
        $domain = $tenant->domains->first();

        if (! $domain) {
            Alert::toast(__('Tenant domain not found'), 'error');
            return redirect()->back();
        }

        $protocol = request()->secure() ? 'https' : 'http';
        $tenantUrl = $protocol . '://' . $domain->domain;

        return redirect($tenantUrl);
    }

    public function toggleStatus($id)
    {
        try {
            $tenant = Tenant::findOrFail($id);
            $tenant->update(['status' => !$tenant->status]);
            Alert::toast(__('Status updated successfully'), 'success');
            return redirect()->back();
        } catch (\Exception $e) {
            Alert::toast(__('Failed to update status'), 'error');
            return redirect()->back();
        }
    }

    /**
     * Get full domain from subdomain.
     */
    private function getFullDomain(string $subdomain): string
    {
        $baseDomain = parse_url(config('app.url'), PHP_URL_HOST);

        // If on localhost or 127.0.0.1, return subdomain.localhost
        if (! $baseDomain || in_array($baseDomain, ['localhost', '127.0.0.1'])) {
            return $subdomain . '.localhost';
        }

        // Remove 'main.' from the base domain if it exists (e.g., main.massar.test -> massar.test)
        $cleanBaseDomain = preg_replace('/^main\./', '', $baseDomain);

        return $subdomain . '.' . $cleanBaseDomain;
    }

    /**
     * Get tenant URL from domain.
     */
    private function getTenantUrl(string $domain): string
    {
        $protocol = request()->secure() ? 'https' : 'http';

        return $protocol . '://' . $domain;
    }
}
