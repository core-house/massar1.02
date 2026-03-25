<?php

declare(strict_types=1);

namespace Modules\Decumintations\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $fileRules = $this->isMethod('POST')
            ? ['required', 'file', 'max:20480'] // 20MB
            : ['nullable', 'file', 'max:20480'];

        return [
            'title'           => ['required', 'string', 'max:255'],
            'description'     => ['nullable', 'string', 'max:2000'],
            'category_id'     => ['nullable', 'exists:document_categories,id'],
            'file'            => $fileRules,
            'tags'            => ['nullable', 'array'],
            'tags.*'          => ['string', 'max:50'],
            'expiry_date'     => ['nullable', 'date'],
            'is_confidential' => ['boolean'],
        ];
    }
}
