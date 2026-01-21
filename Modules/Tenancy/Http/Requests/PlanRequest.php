<?php

declare(strict_types=1);

namespace Modules\Tenancy\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'duration_days' => $this->duration_days ? (int) $this->duration_days : null,
            'max_users' => $this->max_users ? (int) $this->max_users : null,
            'max_branches' => $this->max_branches ? (int) $this->max_branches : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'max_users' => 'nullable|integer|min:1',
            'max_branches' => 'nullable|integer|min:1',
            'status' => 'boolean',
            'features' => 'nullable|array',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('Name'),
            'amount' => __('Amount'),
            'duration_days' => __('Duration Days'),
            'max_users' => __('Max Users'),
            'max_branches' => __('Max Branches'),
            'status' => __('Status'),
            'features' => __('Features'),
        ];
    }
}
