<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Str;

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

    /**
     * Notes: 公共校验接口
     * User: 一颗地梨子
     * DateTime: 2022/4/21 18:20
     * @return string[]
     */
    public function rules()
    {
        // 路由
        $path = $this->route()->getName();

        // 请求方式
        $method = $this->method();

        switch ( strtoupper( $method ) ) {

            case "PUT":

                if ( Str::contains( $path, 'restore' ) ) {
                    /// TODO 还原
                    return $this->getRules ();
                }

            case "DELETE":

                /// TODO 删除
                return $this->getRules ();
        }
    }

    /**
     * Notes: 获取校验信息
     * User: 一颗地梨子
     * DateTime: 2022/4/21 18:18
     * @return string[]
     */
    protected function getRules () {
        return [
            'ids' => "bail|required|array",
        ];
    }

}
