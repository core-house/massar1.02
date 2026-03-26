<?php

namespace Modules\CRM\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientContactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'client_id' => ['required', 'exists:clients,id'],
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'max:255'],
            'phone'     => ['required', 'string', 'max:20'],
            'position'  => ['nullable', 'string', 'max:255'],
            'preferred_contact_method' => ['required', 'in:phone,whatsapp,email'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'client_id' => __('crm::crm.client'),
            'name'      => __('crm::crm.name'),
            'email'     => __('crm::crm.email'),
            'phone'     => __('crm::crm.phone'),
            'position'  => __('crm::crm.position'),
            'preferred_contact_method' => __('crm::crm.preferred_contact_method'),
        ];
    }
}
