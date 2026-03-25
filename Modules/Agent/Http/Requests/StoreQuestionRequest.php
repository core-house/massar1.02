<?php

declare(strict_types=1);

namespace Modules\Agent\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('agent.ask');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'question_text' => ['required', 'string', 'min:5', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'question_text.required' => __('agent.validation.question_required'),
            'question_text.string' => __('agent.validation.question_string'),
            'question_text.min' => __('agent.validation.question_min'),
            'question_text.max' => __('agent.validation.question_max'),
        ];
    }
}
