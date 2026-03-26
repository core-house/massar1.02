<?php

namespace Modules\Quality\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CertificateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'certificate_cost' => $this->certificate_cost !== '' ? $this->certificate_cost : null,
        ]);
    }

    public function rules(): array
    {
        $rules = [
            'certificate_name'  => 'required|string',
            'certificate_type'  => 'required|in:ISO_9001,ISO_22000,HACCP,GMP,HALAL,FDA,CE,custom',
            'custom_type'       => 'nullable|string|required_if:certificate_type,custom',
            'issuing_authority' => 'required|string',
            'expiry_date'       => 'required|date',
            'notification_days' => 'required|numeric|min:1',
            'scope'             => 'nullable|string',
            'notes'             => 'nullable|string',
        ];

        if ($this->isMethod('POST')) {
            $rules['certificate_number'] = 'required|string|unique:quality_certificates,certificate_number';
            $rules['issue_date']         = 'required|date';
            $rules['expiry_date']        = 'required|date|after:issue_date';
            $rules['certificate_cost']   = 'nullable|numeric|min:0';
        }

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['status'] = 'required|in:active,expired,renewal_pending,suspended';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'certificate_number.required' => __('quality::quality.certificate number') . ' ' . __('quality::quality.is required'),
            'certificate_number.unique'   => __('quality::quality.certificate number') . ' ' . __('quality::quality.already exists'),
            'certificate_name.required'   => __('quality::quality.certificate name') . ' ' . __('quality::quality.is required'),
            'certificate_type.required'   => __('quality::quality.certificate type') . ' ' . __('quality::quality.is required'),
            'certificate_type.in'         => __('quality::quality.certificate type') . ' ' . __('quality::quality.is invalid'),
            'custom_type.required_if'     => __('quality::quality.certificate type') . ' ' . __('quality::quality.is required'),
            'issuing_authority.required'  => __('quality::quality.issuing authority') . ' ' . __('quality::quality.is required'),
            'issue_date.required'         => __('quality::quality.issue date') . ' ' . __('quality::quality.is required'),
            'issue_date.date'             => __('quality::quality.issue date') . ' ' . __('quality::quality.must be a valid date'),
            'expiry_date.required'        => __('quality::quality.valid until') . ' ' . __('quality::quality.is required'),
            'expiry_date.date'            => __('quality::quality.valid until') . ' ' . __('quality::quality.must be a valid date'),
            'expiry_date.after'           => __('quality::quality.valid until') . ' ' . __('quality::quality.must be after issue date'),
            'notification_days.required'  => __('quality::quality.notification before expiry (days)') . ' ' . __('quality::quality.is required'),
            'notification_days.min'       => __('quality::quality.notification before expiry (days)') . ' ' . __('quality::quality.must be at least 1'),
            'certificate_cost.numeric'    => __('quality::quality.certificate cost') . ' ' . __('quality::quality.must be a number'),
            'certificate_cost.min'         => __('quality::quality.certificate cost') . ' ' . __('quality::quality.must be at least 0'),
            'status.required'             => __('quality::quality.status') . ' ' . __('quality::quality.is required'),
            'status.in'                   => __('quality::quality.status') . ' ' . __('quality::quality.is invalid'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            redirect()->back()->withErrors($validator)->withInput()
        );
    }
}
