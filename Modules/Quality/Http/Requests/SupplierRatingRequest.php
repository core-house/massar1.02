<?php

namespace Modules\Quality\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SupplierRatingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'quality_score'       => 'required|numeric|min:0|max:100',
            'delivery_score'      => 'required|numeric|min:0|max:100',
            'documentation_score' => 'required|numeric|min:0|max:100',
        ];

        if ($this->isMethod('POST')) {
            $rules['supplier_id']  = 'required|exists:acc_head,id';
            $rules['period_type']  = 'required|in:monthly,quarterly,annual';
            $rules['period_start'] = 'required|date';
            $rules['period_end']   = 'required|date|after:period_start';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'supplier_id.required'        => __('quality::quality.supplier') . ' ' . __('quality::quality.is required'),
            'supplier_id.exists'          => __('quality::quality.supplier') . ' ' . __('quality::quality.is invalid'),
            'period_type.required'        => __('quality::quality.period type') . ' ' . __('quality::quality.is required'),
            'period_type.in'              => __('quality::quality.period type') . ' ' . __('quality::quality.is invalid'),
            'period_start.required'       => __('quality::quality.period start') . ' ' . __('quality::quality.is required'),
            'period_start.date'           => __('quality::quality.period start') . ' ' . __('quality::quality.must be a valid date'),
            'period_end.required'         => __('quality::quality.period end') . ' ' . __('quality::quality.is required'),
            'period_end.date'             => __('quality::quality.period end') . ' ' . __('quality::quality.must be a valid date'),
            'period_end.after'            => __('quality::quality.period end') . ' ' . __('quality::quality.must be after start date'),
            'quality_score.required'      => __('quality::quality.quality score') . ' ' . __('quality::quality.is required'),
            'quality_score.numeric'       => __('quality::quality.quality score') . ' ' . __('quality::quality.must be a number'),
            'quality_score.min'           => __('quality::quality.quality score') . ' ' . __('quality::quality.must be at least 0'),
            'quality_score.max'           => __('quality::quality.quality score') . ' ' . __('quality::quality.must not exceed 100'),
            'delivery_score.required'     => __('quality::quality.delivery score') . ' ' . __('quality::quality.is required'),
            'delivery_score.numeric'      => __('quality::quality.delivery score') . ' ' . __('quality::quality.must be a number'),
            'documentation_score.required' => __('quality::quality.documentation score') . ' ' . __('quality::quality.is required'),
            'documentation_score.numeric' => __('quality::quality.documentation score') . ' ' . __('quality::quality.must be a number'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            redirect()->back()->withErrors($validator)->withInput()
        );
    }
}
