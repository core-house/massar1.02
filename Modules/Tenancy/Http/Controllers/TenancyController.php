<?php

namespace Modules\Tenancy\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Tenancy\Http\Requests\TenantRequest;
use Modules\Tenancy\Models\Domain;
use Modules\Tenancy\Models\Tenant;

class TenancyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tenants = Tenant::with('domains')
            ->latest()
            ->paginate(15);

        return view('tenancy::index', compact('tenants'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tenancy::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TenantRequest $request)
    {
        try {
            DB::beginTransaction();

            // إنشاء التينانت
            $tenant = Tenant::create([
                'id' => $request->subdomain,
                'name' => $request->name,
            ]);

            // إنشاء الدومين
            $domain = $tenant->domains()->create([
                'domain' => $this->getFullDomain($request->subdomain),
            ]);

            DB::commit();

            // بعد إنشاء التينانت، سيتم إنشاء الداتا بيز وتشغيل المايجريشنز تلقائياً
            // من خلال TenantCreated event في TenancyServiceProvider
            // بما أن shouldBeQueued(false)، الـ jobs بتشتغل synchronously في نفس الـ request

            // إعادة التوجيه للسابدومين بعد الإنشاء
            $tenantUrl = $this->getTenantUrl($domain->domain);

            return redirect($tenantUrl)
                ->with('success', __('Tenant created successfully. You are now being redirected to your tenant.'));

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('Failed to create tenant: :message', ['message' => $e->getMessage()]));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $tenant = Tenant::with('domains')->findOrFail($id);

        return view('tenancy::show', compact('tenant'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $tenant = Tenant::with('domains')->findOrFail($id);
        $domain = $tenant->domains->first();

        return view('tenancy::edit', compact('tenant', 'domain'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TenantRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $tenant = Tenant::findOrFail($id);
            $domain = $tenant->domains->first();

            // تحديث اسم التينانت
            $tenant->update([
                'name' => $request->name,
            ]);

            // تحديث السابدومين إذا تغير
            if ($request->subdomain !== $tenant->id) {
                // لا يمكن تغيير ID التينانت بعد الإنشاء
                // لأن الداتا بيز مرتبط بالـ ID
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', __('Cannot change tenant subdomain after creation.'));
            }

            // تحديث الدومين إذا تغير
            $newDomain = $this->getFullDomain($request->subdomain);
            if ($domain && $domain->domain !== $newDomain) {
                $domain->update([
                    'domain' => $newDomain,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('tenancy.index')
                ->with('success', __('Tenant updated successfully.'));

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('Failed to update tenant: :message', ['message' => $e->getMessage()]));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $tenant = Tenant::findOrFail($id);

            // حذف التينانت (سيتم حذف الداتا بيز تلقائياً من خلال TenantDeleted event)
            $tenant->delete();

            return redirect()
                ->route('tenancy.index')
                ->with('success', __('Tenant deleted successfully.'));

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', __('Failed to delete tenant: :message', ['message' => $e->getMessage()]));
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
            return redirect()
                ->back()
                ->with('error', __('Tenant domain not found.'));
        }

        // الحصول على البروتوكول (http أو https)
        $protocol = request()->secure() ? 'https' : 'http';

        // بناء URL للسابدومين
        $tenantUrl = $protocol.'://'.$domain->domain;

        return redirect($tenantUrl);
    }

    /**
     * Get full domain from subdomain.
     */
    private function getFullDomain(string $subdomain): string
    {
        $baseDomain = parse_url(config('app.url'), PHP_URL_HOST);

        // إذا كان baseDomain فارغ أو localhost، استخدم .localhost
        if (! $baseDomain || in_array($baseDomain, ['localhost', '127.0.0.1'])) {
            return $subdomain.'.localhost';
        }

        return $subdomain.'.'.$baseDomain;
    }

    /**
     * Get tenant URL from domain.
     */
    private function getTenantUrl(string $domain): string
    {
        $protocol = request()->secure() ? 'https' : 'http';

        return $protocol.'://'.$domain;
    }
}
