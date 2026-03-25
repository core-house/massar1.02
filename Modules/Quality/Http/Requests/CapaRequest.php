<?php

namespace Modules\Quality\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CapaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'action_description'      => 'required|string',
            'root_cause_analysis'     => 'nullable|string',
            'preventive_measures'     => 'nullable|string',
            'responsible_person'      => 'required|exists:users,id',
            'planned_completion_date' => 'required|date',
        ];

        if ($this->isMethod('POST')) {
            $rules['ncr_id']             = 'required|exists:non_conformance_reports,id';
            $rules['action_type']        = 'required|in:corrective,preventive';
            $rules['planned_start_date'] = 'required|date';
            $rules['planned_completion_date'] = 'required|date|after:planned_start_date';
        }

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['status']                = 'required';
            $rules['completion_percentage'] = 'nullable|integer|min:0|max:100';
            $rules['implementation_notes']  = 'nullable|string';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'ncr_id.required'                      => __('quality::quality.related ncr') . ' ' . __('quality::quality.is required'),
            'ncr_id.exists'                        => __('quality::quality.related ncr') . ' ' . __('quality::quality.is invalid'),
            'action_type.required'                 => __('quality::quality.capa type') . ' ' . __('quality::quality.is required'),
            'action_type.in'                       => __('quality::quality.capa type') . ' ' . __('quality::quality.is invalid'),
            'action_description.required'          => __('quality::quality.action description') . ' ' . __('quality::quality.is required'),
            'responsible_person.required'          => __('quality::quality.assigned to') . ' ' . __('quality::quality.is required'),
            'responsible_person.exists'            => __('quality::quality.assigned to') . ' ' . __('quality::quality.is invalid'),
            'planned_start_date.required'          => __('quality::quality.planned start date') . ' ' . __('quality::quality.is required'),
            'planned_start_date.date'              => __('quality::quality.planned start date') . ' ' . __('quality::quality.must be a valid date'),
            'planned_completion_date.required'     => __('quality::quality.planned completion date') . ' ' . __('quality::quality.is required'),
            'planned_completion_date.date'         => __('quality::quality.planned completion date') . ' ' . __('quality::quality.must be a valid date'),
            'planned_completion_date.after'        => __('quality::quality.planned completion date') . ' ' . __('quality::quality.must be after start date'),
            'status.required'                      => __('quality::quality.status') . ' ' . __('quality::quality.is required'),
            'completion_percentage.integer'        => __('quality::quality.completion percentage') . ' ' . __('quality::quality.must be a number'),
            'completion_percentage.min'            => __('quality::quality.completion percentage') . ' ' . __('quality::quality.must be at least 0'),
            'completion_percentage.max'            => __('quality::quality.completion percentage') . ' ' . __('quality::quality.must not exceed 100'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            redirect()->back()->withErrors($validator)->withInput()
        );
    }
}
