<?php

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

        // للـ create: تحقق من أن subdomain غير موجود
        // للـ update: تحقق من أن subdomain غير موجود إلا للتينانت الحالي
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

        return [
            'subdomain' => $subdomainRules,
            'name' => ['required', 'string', 'max:255'],
        ];
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
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'subdomain' => __('Subdomain'),
            'name' => __('Company Name'),
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'subdomain.regex' => __('The subdomain must contain only lowercase letters, numbers, and hyphens, and cannot start or end with a hyphen.'),
            'subdomain.unique' => __('This subdomain is already taken.'),
        ];
    }
}
