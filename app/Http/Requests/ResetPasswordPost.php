<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordPost extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
            'password'=>'required|min:6',
            'code'=>'required',
            'phone'=>'required',
//            'username'=>'required'
        ];
    }
    public function messages()
    {
        return [
            'password.required'=>'密码不允许为空！',
            'code.required'=>'验证码不能为空！',
            'phone.required'=>'手机号不能为空！',
//            'username.required'=>'用户名不能为空！',
            'password.min'=>'密码不得短于6位',
        ];
    }
}
