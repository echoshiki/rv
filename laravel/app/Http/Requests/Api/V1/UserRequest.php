<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\User;

class UserRequest extends FormRequest
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
            'sex' => 'nullable|in:1,2|max:50',
            'province' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:20',
            'birthday' => 'nullable|date|before:today',
            'address' => 'nullable|string|max:200',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '名称不能为空',
            'name.max' => '名称长度不能超过50个字符',
            'sex.in' => '性别格式不正确',
            'province.max' => '省份长度不能超过20个字符',
            'city.max' => '城市长度不能超过20个字符',
            'birthday.date' => '生日格式不正确',
            'birthday.before' => '生日不能是未来日期',
            'address.max' => '详细地址长度不能超过200个字符',
        ];
    }
}
