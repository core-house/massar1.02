<?php

namespace Modules\Progress\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectTemplateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'project_type_id' => 'nullable|exists:project_types,id',
            'description' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.work_item_id' => 'required|exists:work_items,id',
            'items.*.default_quantity' => 'required|numeric|min:0',
            'items.*.estimated_daily_qty' => 'nullable|numeric|min:0',
            'items.*.subproject_name' => 'nullable|string|max:255',
            'items.*.notes' => 'nullable|string',
            'items.*.is_measurable' => 'boolean',
            'items.*.duration' => 'nullable|numeric|min:0',
            'items.*.dependency_type' => 'nullable|string|in:end_to_start,start_to_start',
            'items.*.lag' => 'nullable|numeric',
            'items.*.predecessor' => 'nullable',
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
