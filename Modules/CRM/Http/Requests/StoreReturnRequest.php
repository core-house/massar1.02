<?php

namespace Modules\CRM\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReturnRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'client_id'               => 'required|exists:clients,id',
            'return_date'             => 'required|date',
            'return_type'             => 'required|in:refund,exchange,credit_note',
            'original_invoice_number' => 'nullable|string|max:255',
            'original_invoice_date'   => 'nullable|date',
            'branch_id'               => 'required|exists:branches,id',
            'reason'                  => 'nullable|string',
            'notes'                   => 'nullable|string',
            'attachment'              => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'images'                  => 'nullable|array|max:5',
            'images.*'                => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
            'items'                   => 'required|array|min:1',
            'items.*.item_id'         => 'required|exists:items,id',
            'items.*.quantity'        => 'required|integer|min:1',
            'items.*.unit_price'      => 'required|numeric|min:0',
            'items.*.item_condition'  => 'nullable|string',
            'items.*.notes'           => 'nullable|string',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('items')) {
            $items = $this->items;

            foreach ($items as $key => $item) {
                if (isset($item['quantity'])) {
                    // نحول "1.00" إلى 1 ليقبلها الـ validation integer
                    $items[$key]['quantity'] = (int) floatval($item['quantity']);
                }
            }

            $this->merge(['items' => $items]);
        }
    }

    public function attributes()
    {
        return [
            'client_id' => __('crm::crm.client'),
            'branch_id' => __('crm::crm.branch'),
            'items' => __('crm::crm.return_items'),
            'items.*.item_id' => __('crm::crm.item'),
            'items.*.quantity' => __('crm::crm.quantity'),
            'return_date' => __('crm::crm.return_date'),
            'return_type' => __('crm::crm.return_type'),
            'original_invoice_number' => __('crm::crm.original_invoice_number'),
            'original_invoice_date' => __('crm::crm.original_invoice_date'),
            'reason' => __('crm::crm.reason'),
            'notes' => __('crm::crm.notes'),
            'attachment' => __('crm::crm.attachment'),
            'images' => __('crm::crm.return_images'),
            'items.*.unit_price' => __('crm::crm.unit_price'),
            'items.*.item_condition' => __('crm::crm.condition'),
            'items.*.notes' => __('crm::crm.notes'),
        ];
    }
}
