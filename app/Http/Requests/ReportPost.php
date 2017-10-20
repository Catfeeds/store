<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;

class ReportPost extends FormRequest
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
            'contact'=>'required',
            'phone'=>'required',
            'commodity_id'=>'required',
            'description'=>'nullable',
            'type_id'=>'required'
        ];
    }
    public function messages()
    {
        return [
            'contact.required'=>'联系人不能为空！',
            'phone.required'=>'联系电话不能为空！',
            'commodity_id'=>'信息编号不能为空！',
            'type_id.required'=>'举报类型不能为空！'
        ];
    }
    protected function formatErrors(Validator $validator)
    {
        $message = $validator->errors()->first();
        return [$message];
    }
    public function response(array $errors)
    {
        return new JsonResponse([
            'return_msg'=>$errors[0],
            'return_code'=>'FAIL'
        ]);
    }
}
