<?php

namespace Modules\Quality\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class InspectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'item_id'          => 'required|exists:items,id',
            'inspection_type'  => 'required|in:receiving,in_process,final,random,customer_complaint',
            'inspection_date'  => 'required|date',
            'quantity_inspected' => 'required|numeric|min:0',
            'pass_quantity'    => 'required|numeric|min:0',
            'fail_quantity'    => 'required|numeric|min:0',
            'result'           => 'required|in:pass,fail,conditional',
            'action_taken'     => 'required',
            'supplier_id'      => 'nullable|exists:acc_head,id',
            'batch_number'     => 'nullable|string|max:255',
            'defects_found'    => 'nullable|string',
            'inspector_notes'  => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'item_id.required'           => __('quality::quality.item') . ' ' . __('quality::quality.is required'),
            'item_id.exists'             => __('quality::quality.item') . ' ' . __('quality::quality.is invalid'),
            'inspection_type.required'   => __('quality::quality.inspection type') . ' ' . __('quality::quality.is required'),
            'inspection_type.in'         => __('quality::quality.inspection type') . ' ' . __('quality::quality.is invalid'),
            'inspection_date.required'   => __('quality::quality.inspection date') . ' ' . __('quality::quality.is required'),
            'inspection_date.date'       => __('quality::quality.inspection date') . ' ' . __('quality::quality.must be a valid date'),
            'quantity_inspected.required' => __('quality::quality.inspected quantity') . ' ' . __('quality::quality.is required'),
            'quantity_inspected.numeric' => __('quality::quality.inspected quantity') . ' ' . __('quality::quality.must be a number'),
            'pass_quantity.required'     => __('quality::quality.passed quantity') . ' ' . __('quality::quality.is required'),
            'fail_quantity.required'     => __('quality::quality.failed quantity') . ' ' . __('quality::quality.is required'),
            'result.required'            => __('quality::quality.result') . ' ' . __('quality::quality.is required'),
            'result.in'                  => __('quality::quality.result') . ' ' . __('quality::quality.is invalid'),
            'action_taken.required'      => __('quality::quality.action taken') . ' ' . __('quality::quality.is required'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            redirect()->back()->withErrors($validator)->withInput()
        );
    }
}
