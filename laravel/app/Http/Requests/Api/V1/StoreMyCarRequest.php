<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMyCarRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['exists:users,id'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'province' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'brand' => ['required', 'string', 'max:255'],
            'vin' => ['required', 'string', 'max:255', Rule::unique('my_cars', 'vin')],
            'licence_plate' => ['required', 'string', 'max:255', Rule::unique('my_cars', 'licence_plate')],
            'listing_at' => ['nullable', 'date'],
            'birthday' => ['nullable', 'date'],
            'address' => ['nullable', 'string', 'max:65535'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '姓名不能为空。',
            'phone.required' => '手机号不能为空。',
            'brand.required' => '车型不能为空。',
            'vin.required' => '车架号不能为空。',
            'vin.unique' => '该车架号已存在。',
            'licence_plate.required' => '车牌号不能为空。',
            'licence_plate.unique' => '该车牌号已存在。',
            // ... 其他自定义消息
        ];
    }
}
