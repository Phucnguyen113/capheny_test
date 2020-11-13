<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class storeRequest extends FormRequest
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
            'store_name' => 'required|bail',
            'province'   => 'required|bail|not_in:0',
            'district'   => 'required|bail|not_in:0',
            'ward'       => 'required|bail|not_in:0',
        ];
    }
    public function messages()
    {
        return [
            'required' => ':attribute không được trống',
            'not_in'   => 'Chưa chọn:attribute '
        ];
    }
    public function attributes()
    {
        return [
            'store_name' => 'Tên cửa hàng',
            'province'   => 'Thành phố, tỉnh',
            'district'   => 'Quận, huyện',
            'ward'       => 'Khu vực'
        ];
    }
   
}
