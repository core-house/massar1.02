<?php

namespace Modules\Quality\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StandardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $standardId = $this->route('standard')?->id;

        return [
            'item_id'              => 'required|exists:items,id',
            'standard_code'        => 'required|string|unique:quality_standards,standard_code,' . $standardId,
            'standard_name'        => 'required|string',
            'description'          => 'nullable|string',
            'test_method'          => 'nullable|string',
            'sample_size'          => 'required|min:1',
            'test_frequency'       => 'required|in:per_batch,daily,weekly,monthly',
            'acceptance_threshold' => 'required|numeric|min:0|max:100',
            'max_defects_allowed'  => 'required|min:0',
            'specifications'       => 'nullable|array',
            'chemical_properties'  => 'nullable|array',
            'physical_properties'  => 'nullable|array',
            'is_active'            => 'boolean',
            'notes'                => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'item_id.required'              => __('quality::quality.item') . ' ' . __('quality::quality.is required'),
            'item_id.exists'                => __('quality::quality.item') . ' ' . __('quality::quality.is invalid'),
            'standard_code.required'        => __('quality::quality.standard code') . ' ' . __('quality::quality.is required'),
            'standard_code.unique'          => __('quality::quality.standard code') . ' ' . __('quality::quality.already exists'),
            'standard_name.required'        => __('quality::quality.standard name') . ' ' . __('quality::quality.is required'),
            'sample_size.required'          => __('quality::quality.sample size') . ' ' . __('quality::quality.is required'),
            'sample_size.min'               => __('quality::quality.sample size') . ' ' . __('quality::quality.must be at least 1'),
            'test_frequency.required'       => __('quality::quality.test frequency') . ' ' . __('quality::quality.is required'),
            'test_frequency.in'             => __('quality::quality.test frequency') . ' ' . __('quality::quality.is invalid'),
            'acceptance_threshold.required' => __('quality::quality.acceptance threshold') . ' ' . __('quality::quality.is required'),
            'acceptance_threshold.numeric'  => __('quality::quality.acceptance threshold') . ' ' . __('quality::quality.must be a number'),
            'acceptance_threshold.min'      => __('quality::quality.acceptance threshold') . ' ' . __('quality::quality.must be at least 0'),
            'acceptance_threshold.max'      => __('quality::quality.acceptance threshold') . ' ' . __('quality::quality.must not exceed 100'),
            'max_defects_allowed.required'  => __('quality::quality.max allowed defects') . ' ' . __('quality::quality.is required'),
            'max_defects_allowed.min'       => __('quality::quality.max allowed defects') . ' ' . __('quality::quality.must be at least 0'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            redirect()->back()->withErrors($validator)->withInput()
        );
    }
}
