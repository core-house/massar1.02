<?php

namespace Modules\CRM\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LeadStatusRequest extends FormRequest
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
        // Assuming the route parameter is 'lead_status' which could be an ID or a model instance
        $id = $this->route('lead_status');

        return [
            'name'         => ['required', 'string', 'max:255'],
            'order_column' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('lead_statuses', 'order_column')->ignore($id),
            ],
            'color'        => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'branch_id'    => 'nullable|exists:branches,id',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name'         => __('crm::crm.name'),
            'order_column' => __('crm::crm.order'),
            'color'        => __('crm::crm.color'),
            'branch_id'    => __('crm::crm.branch'),
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'color.regex' => __('crm::crm.the_color_must_be_valid_hex'),
            'order_column.unique' => __('crm::crm.this_order_is_already_taken'),
        ];
    }
}
