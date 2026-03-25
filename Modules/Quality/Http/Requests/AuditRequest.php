<?php

namespace Modules\Quality\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AuditRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'audit_title'         => 'required|string',
            'planned_date'        => 'required|date',
            'lead_auditor_id'     => 'required|exists:users,id',
            'audit_objectives'    => 'nullable|string',
            'external_auditor'    => 'nullable|string',
            'external_organization' => 'nullable|string',
        ];

        if ($this->isMethod('POST')) {
            $rules['audit_type'] = 'required|in:internal,external,supplier,certification,customer';
            $rules['audit_team'] = 'nullable|array';
        }

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['status'] = 'required|in:planned,in_progress,completed,cancelled';
            $rules['total_findings']    = 'nullable|integer';
            $rules['critical_findings'] = 'nullable|integer';
            $rules['major_findings']    = 'nullable|integer';
            $rules['minor_findings']    = 'nullable|integer';
            $rules['overall_result']    = 'nullable|string';
            $rules['summary']           = 'nullable|string';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'audit_title.required'      => __('quality::quality.audit title') . ' ' . __('quality::quality.is required'),
            'audit_type.required'       => __('quality::quality.audit type') . ' ' . __('quality::quality.is required'),
            'audit_type.in'             => __('quality::quality.audit type') . ' ' . __('quality::quality.is invalid'),
            'planned_date.required'     => __('quality::quality.planned date') . ' ' . __('quality::quality.is required'),
            'planned_date.date'         => __('quality::quality.planned date') . ' ' . __('quality::quality.must be a valid date'),
            'lead_auditor_id.required'  => __('quality::quality.lead auditor') . ' ' . __('quality::quality.is required'),
            'lead_auditor_id.exists'    => __('quality::quality.lead auditor') . ' ' . __('quality::quality.is invalid'),
            'status.required'           => __('quality::quality.status') . ' ' . __('quality::quality.is required'),
            'status.in'                 => __('quality::quality.status') . ' ' . __('quality::quality.is invalid'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            redirect()->back()->withErrors($validator)->withInput()
        );
    }
}
