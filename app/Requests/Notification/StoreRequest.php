<?php

namespace App\Requests\Notification;

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
            'name' => 'required',
            'content' => 'required',
            'user_id' => 'required',
            'type' => 'required',
            'icon_type' =>  'required'
        ];
    }
}