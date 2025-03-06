<?php

namespace App\Requests\Notification;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'nullable',
            'content' => 'nullable',
            'user_id' => 'nullable',
            'type' => 'nullable',
            'icon_type' =>  'nullable'
        ];
    }
}