<?php

namespace Modules\Branches\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BranchesRequest extends FormRequest
{
    public function authorize(): bool
    {
        // هنا ممكن تستخدم صلاحيات مختلفة حسب الدور
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'is_active' => 'required|boolean',
        ];
    }
}
