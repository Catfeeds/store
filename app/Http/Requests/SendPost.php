<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;

class SendPost extends FormRequest
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
            'number' => 'required',
            'type' => 'required|integer'
        ];
    }

    public function messages()
    {
        return [
            'number.required' => '手机号码不允许为空！',
            'type.required' => '类型不能为空！',
            'number.mix' => '手机号码不得短于11位',
            'type.integer' => '类型参数格式错误!',
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