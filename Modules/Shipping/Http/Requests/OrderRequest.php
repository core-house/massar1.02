<?php

namespace Modules\Shipping\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $orderId = $this->route('order')?->id;
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        return [
            'order_number'   => 'required|string|unique:orders,order_number,' . $orderId,
            'driver_id'      => 'required|exists:drivers,id',
            'shipment_id'    => 'required|exists:shipments,id',
            'customer_name'  => 'required|string|max:255',
            'customer_address' => 'required|string',
            'delivery_status'  => 'required|in:pending,assigned,in_transit,delivered',
            'branch_id' => $isUpdate ? 'nullable|exists:branches,id' : 'required|exists:branches,id',
        ];
    }

    public function messages(): array
    {
        return [
            'order_number.required' => __('Order number is required.'),
            'order_number.unique'   => __('Order number already exists.'),

            'driver_id.required' => __('Driver is required.'),
            'driver_id.exists'   => __('Selected driver does not exist.'),

            'shipment_id.required' => __('Shipment is required.'),
            'shipment_id.exists'   => __('Selected shipment does not exist.'),

            'customer_name.required' => __('Customer name is required.'),
            'customer_name.string'   => __('Customer name must be a string.'),
            'customer_name.max'      => __('Customer name must not exceed 255 characters.'),

            'customer_address.required' => __('Customer address is required.'),

            'delivery_status.required' => __('Delivery status is required.'),
            'delivery_status.in'       => __('Delivery status is invalid.'),

            'branch_id.required' => __('Branch is required.'),
            'branch_id.exists' => __('Selected branch is invalid.'),
        ];
    }
}
