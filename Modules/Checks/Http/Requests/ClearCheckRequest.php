<?php

namespace Modules\Checks\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClearCheckRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('clear', $this->route('check'));
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
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
            'bank_account_id.required' => __('checks::checks.bank_account_id_required'),
            'bank_account_id.exists' => __('checks::checks.bank_account_id_exists'),
            'collection_date.required' => __('checks::checks.collection_date_required'),
            'collection_date.date' => __('checks::checks.collection_date_date'),
            'branch_id.required' => __('checks::checks.branch_id_required'),
            'branch_id.exists' => __('checks::checks.branch_id_exists'),
        ];
    }
}
