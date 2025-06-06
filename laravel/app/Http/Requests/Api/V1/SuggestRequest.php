<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SuggestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:50',
            'content' => 'required|string|min:2|max:5000',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '称呼不能为空',
            'name.max' => '称呼长度不能超过50个字符',
            'content.required' => '建议内容不能为空',
            'content.min' => '建议内容长度不能少于10个字符',
            'content.max' => '建议内容长度不能超过5000个字符',
        ];
    }
}
