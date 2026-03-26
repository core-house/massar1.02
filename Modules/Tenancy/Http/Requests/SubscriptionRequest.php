<?php

declare(strict_types=1);

namespace Modules\Tenancy\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tenant_id' => 'required|exists:tenants,id',
            'plan_id' => 'required|exists:plans,id',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'paid_amount' => 'required|numeric|min:0',
            'status' => 'boolean',
        ];
    }

    public function attributes(): array
    {
        return [
            'tenant_id' => __('Tenant'),
            'plan_id' => __('Plan'),
            'starts_at' => __('Starts At'),
            'ends_at' => __('Ends At'),
            'paid_amount' => __('Paid Amount'),
            'status' => __('Status'),
        ];
    }
}
