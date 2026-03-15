<?php

namespace Modules\CRM\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\CRM\Enums\ActivityTypeEnum;

class ActivityRequest extends FormRequest
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
        return [
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'type'          => 'required|in:' . implode(',', ActivityTypeEnum::values()),
            'activity_date' => 'required|date',
            'scheduled_at'  => 'nullable|date_format:H:i',
            'client_id'     => 'nullable|exists:clients,id',
            'assigned_to'   => 'nullable|exists:users,id',
            'branch_id'     => 'nullable|exists:branches,id',
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
            'title'         => __('crm::crm.activity_title'),
            'description'   => __('crm::crm.description'),
            'type'          => __('crm::crm.type'),
            'activity_date' => __('crm::crm.activity_date'),
            'scheduled_at'  => __('crm::crm.time'),
            'client_id'     => __('crm::crm.client'),
            'assigned_to'   => __('crm::crm.assigned_to'),
            'branch_id'     => __('crm::crm.branch'),
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
            'type.in' => __('crm::crm.the_selected_type_is_invalid'),
            'scheduled_at.date_format' => __('crm::crm.the_time_does_not_match_format'),
        ];
    }
}
