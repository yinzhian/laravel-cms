<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CommonRequest extends FormRequest
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
     * Notes: 自定义错误信息
     * User: 一颗地梨子
     * DateTime: 2022/2/14 18:19
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator)
    {
        // 自定义返回值
        throw (new HttpResponseException( fail( (int) 400, (String) $validator->errors()->first(), (int) 400 )));
    }

}
