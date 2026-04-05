<?php

namespace Modules\Checks\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CollectCheckRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $check = $this->route('check');
        
        return $check && $this->user()->can('clear', $check);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'account_type' => ['required', 'in:bank,cash'],
            'account_id' => ['required', 'integer', 'exists:acc_head,id'],
            'collection_date' => ['required', 'date'],
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'account_type.required' => __('checks::checks.account_type_required'),
            'account_type.in' => __('checks::checks.account_type_in'),
            'account_id.required' => __('checks::checks.account_id_required'),
            'account_id.exists' => __('checks::checks.account_id_exists'),
            'collection_date.required' => __('checks::checks.collection_date_required'),
            'collection_date.date' => __('checks::checks.collection_date_date'),
            'branch_id.required' => __('checks::checks.branch_id_required'),
            'branch_id.exists' => __('checks::checks.branch_id_exists'),
        ];
    }
}

