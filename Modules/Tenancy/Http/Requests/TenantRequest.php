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

        // مخصص للتحقق من التضارب بين السيرفرات (VPS vs Shared Host)
        $subdomainRules[] = function ($attribute, $value, $fail) use ($method, $tenantId) {
            $fullDomain = $this->getFullDomain($value);

            // 1. الفحص المحلي في قاعدة بيانات الـ VPS
            if ($method === 'POST') {
                if (Domain::where('domain', $fullDomain)->exists()) {
                    return $fail(__('This subdomain is already registered in our system.'));
                }
            }

            // 2. الفحص الذكي للـ DNS
            // هنا حط الـ IP بتاع الـ Shared Host (الاستضافة المشتركة)
            // لو مش عارفه، هو الـ IP اللي مربوط عليه الـ 50 سابدومين التانيين
            $sharedHostIp = 'ضع_هنا_IP_الاستضافة_المشتركة';

            $targetIp = gethostbyname($fullDomain);

            // لو السابدومين بيرد بـ IP الـ Shared Host، يبقى محجوز هناك
            if ($targetIp === $sharedHostIp) {
                return $fail(__('This subdomain is reserved on the Shared Hosting server.'));
            }

            // إضافة اختيارية: لو السابدومين بيرد بـ IP الـ VPS نفسه (بسبب الـ Wildcard)
            // وفي نفس الوقت مش موجود في قاعدة البيانات، يبقى "متاح" للـ VPS إنه ياخده.
            // الـ IP اللي في صورتك هو 195.35.25.123
            $vpsIp = '195.35.25.123';

            if ($targetIp !== $vpsIp && $targetIp !== $fullDomain) {
                // ده معناه إنه متوجه لسيرفر تالت خالص أو للـ Shared host
                return $fail(__('This subdomain is already pointed to another server.'));
            }
        };

        return [
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
            'status' => ['boolean'],
            'max_users' => ['nullable', 'integer', 'min:1'],
            'max_branches' => ['nullable', 'integer', 'min:1'],
            'enabled_modules' => ['nullable', 'array'],
            'enabled_modules.*' => ['string', 'in:' . implode(',', array_keys(config('modules_list')))],
        ];
    }

    /**
     * Get full domain from subdomain.
     */
    private function getFullDomain(string $subdomain): string
    {
        $url = config('app.url');
        $baseDomain = parse_url($url, PHP_URL_HOST);

        if (! $baseDomain || in_array($baseDomain, ['localhost', '127.0.0.1'])) {
            return $subdomain . '.localhost';
        }

        // إزالة 'main.' أو 'www.' لضمان الحصول على الدومين الصافي (erplock.com)
        $cleanBaseDomain = preg_replace('/^(main\.|www\.)/', '', $baseDomain);

        return $subdomain . '.' . $cleanBaseDomain;
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
            'user_position' => __('User Position'),
            'referral_code' => __('Referral Code'),
            'plan_id' => __('Plan'),
            'status' => __('Status'),
            'max_users' => __('Max Users'),
            'max_branches' => __('Max Branches'),
        ];
    }
}
