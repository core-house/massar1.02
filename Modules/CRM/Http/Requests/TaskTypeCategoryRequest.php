<?php

declare(strict_types=1);

namespace Modules\CRM\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskTypeCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->route('taskTypeCategory')?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('task_type_categories', 'name')->ignore($categoryId),
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('crm::crm.the_category_name_is_required'),
            'name.string' => __('crm::crm.the_category_name_must_be_string'),
            'name.max' => __('crm::crm.the_category_name_must_not_exceed_255'),
            'name.unique' => __('crm::crm.this_category_name_already_exists'),
            'description.string' => __('crm::crm.the_description_must_be_string'),
            'description.max' => __('crm::crm.the_description_must_not_exceed_1000'),
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('crm::crm.category_name'),
            'description' => __('crm::crm.description'),
        ];
    }
}
