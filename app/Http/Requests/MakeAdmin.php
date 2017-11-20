<?php

namespace App\Http\Requests;

//use Dotenv\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Validator;

class MakeAdmin extends FormRequest
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
            'username'=>'required|unique:users|max:255',
            'password'=>'required|min:6',
            'phone'=>'required|unique:users'
        ];
    }
    public function messages()
    {
        return [
            'username.required'=>'用户名不能为空！',
            'username.unique'=>'该用户名已存在！',
            'password.required'=>'密码不能为空！',
            'password.min'=>'密码少于六位！',
            'phone.required'=>'手机号不能为空',
            'phone.unique'=>'该手机号已被绑定！',
            'code.required'=>'验证码不能为空！'
        ];
    }
    protected function formatErrors(Validator $validator)
    {
        $message = $validator->errors()->first();
        return [$message]; // TODO: Change the autogenerated stub
    }
    public function response(array $errors)
    {
        return new JsonResponse([
            'return_msg'=>$errors[0],
            'return_code'=>'FAIL'
        ]);
    }
}
