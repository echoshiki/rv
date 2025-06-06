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

    /**
     * 为验证准备数据。
     * 这个方法会在所有验证规则运行之前被调用。
     * 它是进行数据清洗（如 htmlspecialchars）的理想位置。
     */
    protected function prepareForValidation(): void
    {
        // 使用 merge 方法将处理后的数据重新合并到请求中
        // htmlspecialchars 会将 < > & " ' 等字符转换为 HTML 实体，有效防止 XSS
        // ENT_QUOTES 参数表示同时转换单引号和双引号
        // 'UTF-8' 指定字符编码
        $this->merge([
            'name' => htmlspecialchars($this->input('name'), ENT_QUOTES, 'UTF-8'),
            'issues' => htmlspecialchars($this->input('issues'), ENT_QUOTES, 'UTF-8'),
        ]);
    }
}
