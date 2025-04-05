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
            'email'                => 'nullable',
            'profile_photo'        => 'nullable',
            'mobile_no'            => 'nullable',
            'home_no'              => 'nullable',
            'professional_title'   => 'nullable',
            'skills'               => 'nullable',
            'current_address'      => 'nullable',
            'is_notify'            => 'nullable',
            'is_location'          => 'nullable',
            'service_area'         => 'nullable',
            'black_out_from'       => 'nullable|date',
            'black_out_to'         => 'nullable|date|required_with:black_out_from|after:black_out_from',
            'is_blackout'          => 'nullable',
            'black_out_dates'      => 'nullable'
        ];
    }

    public function messages()
    {
        return [
            'black_out_to.required_with' => 'The blackout "to" date is required when a blackout "from" date is provided.',
            'black_out_to.after'         => 'The blackout "to" date must be a date after the blackout "from" date.',
        ];
    }
}
