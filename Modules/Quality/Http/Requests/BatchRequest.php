<?php

namespace Modules\Quality\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class BatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'production_date'  => 'required|date',
            'expiry_date'      => 'nullable|date|after:production_date',
            'supplier_id'      => 'nullable|exists:acc_head,id',
            'warehouse_id'     => 'nullable|exists:acc_head,id',
            'location'         => 'nullable|string',
            'notes'            => 'nullable|string',
        ];

        if ($this->isMethod('POST')) {
            $rules['batch_number']   = 'required|string|unique:batch_tracking,batch_number';
            $rules['item_id']        = 'required|exists:items,id';
            $rules['quantity']       = 'required|numeric|min:0';
            $rules['quality_status'] = 'required|in:passed,failed,conditional,quarantine';
        }

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $batch = $this->route('batch');
            $rules['remaining_quantity'] = 'required|numeric|min:0|max:' . ($batch?->quantity ?? 999999);
            $rules['quality_status']     = 'required';
            $rules['status']             = 'required';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'batch_number.required'      => __('quality::quality.batch number') . ' ' . __('quality::quality.is required'),
            'batch_number.unique'        => __('quality::quality.batch number') . ' ' . __('quality::quality.already exists'),
            'item_id.required'           => __('quality::quality.item') . ' ' . __('quality::quality.is required'),
            'item_id.exists'             => __('quality::quality.item') . ' ' . __('quality::quality.is invalid'),
            'production_date.required'   => __('quality::quality.production date') . ' ' . __('quality::quality.is required'),
            'production_date.date'       => __('quality::quality.production date') . ' ' . __('quality::quality.must be a valid date'),
            'expiry_date.date'           => __('quality::quality.expiry date') . ' ' . __('quality::quality.must be a valid date'),
            'expiry_date.after'          => __('quality::quality.expiry date') . ' ' . __('quality::quality.must be after production date'),
            'quantity.required'          => __('quality::quality.quantity') . ' ' . __('quality::quality.is required'),
            'quantity.numeric'           => __('quality::quality.quantity') . ' ' . __('quality::quality.must be a number'),
            'quality_status.required'    => __('quality::quality.quality status') . ' ' . __('quality::quality.is required'),
            'quality_status.in'          => __('quality::quality.quality status') . ' ' . __('quality::quality.is invalid'),
            'remaining_quantity.required' => __('quality::quality.remaining quantity') . ' ' . __('quality::quality.is required'),
            'remaining_quantity.max'     => __('quality::quality.remaining quantity') . ' ' . __('quality::quality.must not exceed original quantity'),
            'status.required'            => __('quality::quality.status') . ' ' . __('quality::quality.is required'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            redirect()->back()->withErrors($validator)->withInput()
        );
    }
}
