<?php

declare(strict_types=1);

namespace Modules\Decumintations\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'color'       => ['nullable', 'string', 'max:20'],
            'icon'        => ['nullable', 'string', 'max:50'],
        ];
    }
}
