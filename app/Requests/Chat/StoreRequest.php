<?php

namespace App\Requests\Chat;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            // 'from_user_id' => 'required',
            'message' => 'required',
        ];
    }
}