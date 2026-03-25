<?php

namespace Modules\Quality\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class NcrRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'estimated_cost' => $this->estimated_cost !== '' ? $this->estimated_cost : null,
        ]);
    }

    public function rules(): array
    {
        $rules = [
            'item_id'             => 'required|exists:items,id',
            'batch_number'        => 'nullable|string',
            'affected_quantity'   => 'required|numeric|min:0',
            'source'              => 'required',
            'detected_date'       => 'required|date',
            'problem_description' => 'required|string',
            'severity'            => 'required|in:critical,major,minor',
            'estimated_cost'      => 'nullable|numeric|min:0',
            'immediate_action'    => 'nullable|string',
            'disposition'         => 'nullable',
            'assigned_to'         => 'nullable|exists:users,id',
            'target_closure_date' => 'nullable|date',
            'attachments'         => 'nullable|array',
        ];

        if ($this->isMethod('POST')) {
            $rules['inspection_id'] = 'nullable|exists:quality_inspections,id';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'item_id.required'             => __('quality::quality.item') . ' ' . __('quality::quality.is required'),
            'item_id.exists'               => __('quality::quality.item') . ' ' . __('quality::quality.is invalid'),
            'affected_quantity.required'   => __('quality::quality.affected quantity') . ' ' . __('quality::quality.is required'),
            'affected_quantity.numeric'    => __('quality::quality.affected quantity') . ' ' . __('quality::quality.must be a number'),
            'source.required'              => __('quality::quality.source') . ' ' . __('quality::quality.is required'),
            'detected_date.required'       => __('quality::quality.detection date') . ' ' . __('quality::quality.is required'),
            'detected_date.date'           => __('quality::quality.detection date') . ' ' . __('quality::quality.must be a valid date'),
            'problem_description.required' => __('quality::quality.description') . ' ' . __('quality::quality.is required'),
            'severity.required'            => __('quality::quality.severity') . ' ' . __('quality::quality.is required'),
            'severity.in'                  => __('quality::quality.severity') . ' ' . __('quality::quality.is invalid'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            redirect()->back()->withErrors($validator)->withInput()
        );
    }
}
