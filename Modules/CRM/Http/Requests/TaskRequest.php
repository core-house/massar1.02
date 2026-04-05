<?php

namespace Modules\CRM\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Modules\CRM\Enums\TaskPriorityEnum;
use Modules\CRM\Enums\TaskStatusEnum;

class TaskRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        // If sending to all users, user_id is not required
        $userIdRule = $this->input('send_to_all_users') == '1'
            ? ['nullable', 'exists:users,id']
            : ['required', 'exists:users,id'];

        return [
            'client_id' => ['nullable', 'exists:clients,id'],
            'user_id' => $userIdRule,
            'send_to_all_users' => ['nullable', 'boolean'],
            'task_type_id' => ['required', 'exists:task_types,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', new Enum(TaskStatusEnum::class)],
            'priority' => ['required', new Enum(TaskPriorityEnum::class)],
            'start_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'duration' => ['nullable', 'numeric', 'min:0'],
            'client_comment' => ['nullable', 'string'],
            'user_comment' => ['nullable', 'string'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'max:5120', 'mimes:jpg,jpeg,png,pdf,doc,docx'],
            'branch_id' => $isUpdate ? 'nullable|exists:branches,id' : 'required|exists:branches,id',
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
            'client_id' => __('crm::crm.client'),
            'user_id' => __('crm::crm.user'),
            'task_type_id' => __('crm::crm.task_type'),
            'title' => __('crm::crm.task_title'),
            'description' => __('crm::crm.description'),
            'status' => __('crm::crm.status'),
            'priority' => __('crm::crm.priority'),
            'start_date' => __('crm::crm.start_date'),
            'delivery_date' => __('crm::crm.due_date'),
            'duration' => __('crm::crm.duration'),
            'client_comment' => __('crm::crm.client_comment'),
            'user_comment' => __('crm::crm.user_comment'),
            'attachments' => __('crm::crm.attachments'),
            'attachments.*' => __('crm::crm.attachment'),
            'branch_id' => __('crm::crm.branch'),
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
            'delivery_date.after_or_equal' => __('crm::crm.due_date_after_start_date'),
            'attachments.*.mimes' => __('crm::crm.unsupported_file_format'),
            'attachments.*.max' => __('crm::crm.file_size_must_not_exceed_5mb'),
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
