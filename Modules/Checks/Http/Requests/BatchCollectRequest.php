<?php

namespace Modules\Checks\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BatchCollectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('clear', \Modules\Checks\Models\Check::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer', 'exists:checks,id'],
            'bank_account_id' => ['required', 'integer', 'exists:acc_head,id'],
            'collection_date' => ['required', 'date'],
            'branch_id' => ['required', 'exists:branches,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'ids.required' => __('checks::checks.ids_required'),
            'ids.array' => __('checks::checks.ids_array'),
            'ids.min' => __('checks::checks.ids_min'),
            'bank_account_id.required' => __('checks::checks.bank_account_id_required'),
            'collection_date.required' => __('checks::checks.collection_date_required'),
            'branch_id.required' => __('checks::checks.branch_id_required'),
        ];
    }
}
