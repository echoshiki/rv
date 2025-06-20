<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\MyCar;

class MyCarRequest extends FormRequest
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
            'province' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:20',
            'brand' => 'nullable|string|max:50',
            'vin' => [
                'required',
                'string',
                'size:17',
                'regex:/^[A-Za-z0-9]{17}$/',
                // 更新时忽略当前记录
                Rule::unique('my_cars', 'vin')->ignore($this->my_car?->id),
            ],
            'licence_plate' => [
                'required',
                'string',
                'max:10',
                Rule::unique('my_cars', 'licence_plate')->ignore($this->my_car?->id),
            ],
            'listing_at' => 'nullable|date',
            'birthday' => 'nullable|date|before:today',
            'address' => 'nullable|string|max:200',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '姓名不能为空',
            'name.max' => '姓名长度不能超过50个字符',
            'phone.required' => '手机号不能为空',
            'phone.regex' => '手机号格式不正确',
            'brand.max' => '车型长度不能超过50个字符',
            'vin.required' => '车架号不能为空',
            'vin.size' => '车架号必须是17位',
            'vin.regex' => '车架号格式不正确',
            'vin.unique' => '车架号已经被绑定',
            'licence_plate.required' => '车牌号不能为空',
            'licence_plate.max' => '车牌号长度不能超过10个字符',
            'licence_plate.unique' => '车牌号已经被绑定',
            'listing_at.date' => '上牌日期格式不正确',
            'birthday.date' => '生日格式不正确',
            'birthday.before' => '生日不能是未来日期',
            'address.max' => '详细地址长度不能超过200个字符',
        ];
    }
}
