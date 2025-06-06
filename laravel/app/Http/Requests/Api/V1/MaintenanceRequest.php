<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class MaintenanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:50',
            'phone' => 'required|string|regex:/^1[3-9]\d{9}$/',
            'province' => 'required|string|max:20',
            'city' => 'required|string|max:20',
            'issues' => 'required|string|min:2|max:5000',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '姓名不能为空',
            'name.max' => '姓名长度不能超过50个字符',
            'phone.required' => '手机号不能为空',
            'phone.regex' => '手机号格式不正确',
            'issues.required' => '维保事项不能为空',
            'issues.min' => '维保事项长度不能少于10个字符',
            'issues.max' => '维保事项长度不能超过5000个字符',
        ];
    }

}
