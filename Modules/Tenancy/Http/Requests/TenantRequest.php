<?php

declare(strict_types=1);

namespace Modules\Tenancy\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Tenancy\Models\Domain;

class TenantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $fields = ['max_users', 'max_branches'];
        $merge = [];
        foreach ($fields as $field) {
            if ($this->has($field)) {
                $merge[$field] = $this->$field ? (int) $this->$field : null;
            }
        }
        if (!empty($merge)) {
            $this->merge($merge);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $tenantId = $this->route('tenancy');
        $method = $this->method();

        $subdomainRules = [
            'required',
            'string',
            'max:63',
            'regex:/^[a-z0-9]([a-z0-9-]*[a-z0-9])?$/',
        ];

        if ($method === 'POST') {
            $subdomainRules[] = function ($attribute, $value, $fail) {
                $fullDomain = $this->getFullDomain($value);
                if (Domain::where('domain', $fullDomain)->exists()) {
                    $fail(__('This subdomain is already taken.'));
                }
            };
        } else {
            $subdomainRules[] = function ($attribute, $value, $fail) use ($tenantId) {
                if ($tenantId) {
                    $fullDomain = $this->getFullDomain($value);
                    $exists = Domain::where('domain', $fullDomain)
                        ->where('tenant_id', '!=', $tenantId)
                        ->exists();
                    if ($exists) {
                        $fail(__('This subdomain is already taken.'));
                    }
                }
            };
        }

        $rules = [
            'subdomain' => $subdomainRules,
            'name' => ['required', 'string', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'company_size' => ['nullable', 'string', 'max:50'],
            'admin_email' => ['required', 'email', 'max:255'],
            'user_position' => ['nullable', 'string', 'max:100'],
            'referral_code' => ['nullable', 'string', 'max:50'],
            'plan_id' => ['required', 'exists:plans,id'],
            'subscription_start_at' => ['nullable', 'date'],
            'subscription_end_at' => ['nullable', 'date', 'after_or_equal:subscription_start_at'],
            'status' => ['boolean'],
            'max_users' => ['nullable', 'integer', 'min:1'],
            'max_branches' => ['nullable', 'integer', 'min:1'],
        ];

        if ($method === 'POST') {
            $rules['admin_password'] = ['required', 'string', 'min:8'];
        } else {
            $rules['admin_password'] = ['nullable', 'string', 'min:8'];
        }

        return $rules;
    }

    /**
     * Get full domain from subdomain.
     */
    private function getFullDomain(string $subdomain): string
    {
        $baseDomain = parse_url(config('app.url'), PHP_URL_HOST);

        if (! $baseDomain || in_array($baseDomain, ['localhost', '127.0.0.1'])) {
            return $subdomain.'.localhost';
        }

        return $subdomain.'.'.$baseDomain;
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'subdomain' => __('Subdomain'),
            'name' => __('Name'),
            'contact_number' => __('Contact Number'),
            'address' => __('Address'),
            'company_name' => __('Company Name'),
            'company_size' => __('Company Size'),
            'admin_email' => __('Admin Email'),
            'admin_password' => __('Admin Password'),
            'user_position' => __('User Position'),
            'referral_code' => __('Referral Code'),
            'plan_id' => __('Plan'),
            'subscription_start_at' => __('Subscription Start'),
            'subscription_end_at' => __('Subscription End'),
            'status' => __('Status'),
            'max_users' => __('Max Users'),
            'max_branches' => __('Max Branches'),
        ];
    }
}
