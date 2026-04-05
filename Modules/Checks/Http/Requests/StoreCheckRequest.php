<?php

namespace Modules\Checks\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCheckRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', \Modules\Checks\Models\Check::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'pro_date' => ['required', 'date'],
            'check_number' => ['required', 'string', 'max:50', 'unique:checks'],
            'bank_name' => ['required', 'string', 'max:100'],
            'account_number' => ['nullable', 'string', 'max:50'],
            'account_holder_name' => ['required', 'string', 'max:100'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'issue_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:issue_date'],
            'status' => ['nullable', 'in:pending,cleared,bounced,cancelled'],
            'type' => ['required', 'in:incoming,outgoing'],
            'payee_name' => ['nullable', 'string', 'max:100'],
            'payer_name' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
            'reference_number' => ['nullable', 'string', 'max:50'],
            'acc1_id' => ['required', 'integer', 'exists:acc_head,id'],
            'portfolio_id' => ['required', 'integer', 'exists:acc_head,id'],
            'branch_id' => ['required', 'exists:branches,id'],
            'acc2_before' => ['nullable', 'numeric'],
            'acc2_after' => ['nullable', 'numeric'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'check_number.required' => __('checks::checks.check_number_required'),
            'check_number.unique' => __('checks::checks.check_number_exists'),
            'bank_name.required' => __('checks::checks.bank_name_required'),
            'account_number.required' => __('checks::checks.account_number_required'),
            'account_holder_name.required' => __('checks::checks.account_holder_name_required'),
            'amount.required' => __('checks::checks.amount_required'),
            'amount.numeric' => __('checks::checks.amount_numeric'),
            'amount.min' => __('checks::checks.amount_min'),
            'issue_date.required' => __('checks::checks.issue_date_required'),
            'due_date.required' => __('checks::checks.due_date_required'),
            'due_date.after_or_equal' => __('checks::checks.due_date_after_or_equal'),
            'acc1_id.required' => __('checks::checks.acc1_id_required'),
            'portfolio_id.required' => __('checks::checks.portfolio_id_required'),
            'branch_id.required' => __('checks::checks.branch_id_required'),
        ];
    }
}
