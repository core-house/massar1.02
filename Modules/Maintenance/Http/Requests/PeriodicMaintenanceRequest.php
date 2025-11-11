<?php

namespace Modules\Maintenance\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PeriodicMaintenanceRequest extends FormRequest
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
            'item_name' => 'required|string|max:255',
            'item_number' => 'required|string|max:255',
            'client_name' => 'required|string|max:255',
            'client_phone' => 'required|string|max:255',
            'service_type_id' => 'required|exists:service_types,id',
            'frequency_type' => 'required|in:daily,weekly,monthly,quarterly,semi_annual,annual,custom_days',
            'frequency_value' => 'required_if:frequency_type,custom_days|nullable|integer|min:1',
            'start_date' => 'required|date',
            'notification_days_before' => 'required|integer|min:1|max:365',
            'is_active' => 'nullable|boolean',
            'notes' => 'nullable|string',
            'branch_id' => 'nullable|exists:branches,id',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'item_name' => 'اسم البند',
            'item_number' => 'رقم البند',
            'client_name' => 'اسم العميل',
            'client_phone' => 'رقم التليفون',
            'service_type_id' => 'نوع الصيانة',
            'frequency_type' => 'تكرار الصيانة',
            'frequency_value' => 'عدد الأيام',
            'start_date' => 'تاريخ البداية',
            'notification_days_before' => 'التنبيه قبل',
            'is_active' => 'حالة الجدول',
            'notes' => 'الملاحظات',
            'branch_id' => 'الفرع',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'item_name.required' => 'يجب إدخال اسم البند',
            'item_number.required' => 'يجب إدخال رقم البند',
            'client_name.required' => 'يجب إدخال اسم العميل',
            'client_phone.required' => 'يجب إدخال رقم التليفون',
            'service_type_id.required' => 'يجب اختيار نوع الصيانة',
            'service_type_id.exists' => 'نوع الصيانة المحدد غير موجود',
            'frequency_type.required' => 'يجب اختيار تكرار الصيانة',
            'frequency_type.in' => 'نوع التكرار المحدد غير صحيح',
            'frequency_value.required_if' => 'يجب إدخال عدد الأيام عند اختيار مدة مخصصة',
            'frequency_value.integer' => 'عدد الأيام يجب أن يكون رقم صحيح',
            'frequency_value.min' => 'عدد الأيام يجب أن يكون على الأقل 1',
            'start_date.required' => 'يجب إدخال تاريخ البداية',
            'start_date.date' => 'تاريخ البداية غير صحيح',
            'notification_days_before.required' => 'يجب إدخال عدد أيام التنبيه',
            'notification_days_before.integer' => 'عدد أيام التنبيه يجب أن يكون رقم صحيح',
            'notification_days_before.min' => 'عدد أيام التنبيه يجب أن يكون على الأقل 1',
            'notification_days_before.max' => 'عدد أيام التنبيه يجب ألا يتجاوز 365 يوم',
            'is_active.boolean' => 'حالة الجدول يجب أن تكون نشط أو معطل',
            'branch_id.exists' => 'الفرع المحدد غير موجود',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // تحويل القيم لـ integer للتأكد
        if ($this->has('notification_days_before')) {
            $this->merge([
                'notification_days_before' => (int) $this->notification_days_before,
            ]);
        }

        if ($this->has('frequency_value') && $this->frequency_value) {
            $this->merge([
                'frequency_value' => (int) $this->frequency_value,
            ]);
        }

        // تحويل is_active من string إلى boolean
        if ($this->has('is_active')) {
            $this->merge([
                'is_active' => filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN),
            ]);
        }
    }
}
