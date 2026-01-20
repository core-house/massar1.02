<?php

namespace Modules\Tenancy\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Tenancy\Models\Tenant;

class TenantController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'subdomain' => 'required|unique:domains,domain',
            'company_name' => 'required',
        ]);

        $tenant = Tenant::create([
            'id' => $request->subdomain,
            'name' => $request->company_name,
        ]);

        $tenant->domains()->create([
            'domain' => $request->subdomain.'.localhost',
        ]);

        return redirect()->back()->with('success', 'Company created!');
    }
}
