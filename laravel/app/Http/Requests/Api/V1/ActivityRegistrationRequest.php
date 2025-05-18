<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class ActivityRegistrationRequest extends FormRequest
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
            'activity_id' => 'required|exists:activities,id',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'province' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'form_data' => 'nullable|array',
            'remarks' => 'nullable|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'activity_id.required' => '必须选择一个活动。',
            'activity_id.exists' => '选择的活动无效。',
            'name.required' => '请填写您的姓名。',
            'phone.required' => '请填写您的手机号码。',
        ];
    }
}
