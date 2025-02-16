<?php

namespace App\Requests\User;

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
            'email' => 'nullable',
            'profile_photo' => 'nullable',
            'mobile_no' => 'nullable',
            'home_no' => 'nullable',
            'professional_title' => 'nullable',
            'skills' => 'nullable',
            'current_address' => 'nullable',
            'is_notify' => 'nullable',
            'is_location' => 'nullable',
            'service_area' => 'nullable'
        ];
    }
}