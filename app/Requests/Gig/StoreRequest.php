<?php

namespace App\Requests\Gig;

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
            'type' => ['required', 'in:diagnostic,full-repair'],
            'images' => ['required', 'array', 'min:1'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,gif'], // Each file must be an image & max 2MB
            'selectedRepairs' => [
                'required',
                function ($attribute, $value, $fail) {
                    // Decode JSON string to an array
                    $decodedValue = json_decode($value, true);

                    // Check if decoding failed or it's not an array
                    if (!is_array($decodedValue)) {
                        return $fail('Invalid repair selection format.');
                    }

                    // Ensure array has at least one item
                    if (count($decodedValue) < 1) {
                        return $fail('Please select at least one repair solution.');
                    }
                }
            ],
        ];
    }

    public function messages()
    {
        return [
            'type.required' => 'Please choose Diagnostic or Full Repair.',
            'type.in' => 'Invalid type selected. Choose either "diagnostic" or "full-repair".',
            'images.required' => 'You must upload at least one image.',
            'images.min' => 'You must upload at least one image.',
            'images.*.image' => 'Each file must be an image.',
            'images.*.mimes' => 'Allowed image formats are jpeg, png, jpg, and gif.',
            'images.*.max' => 'Each image must not exceed 2MB.',
            'selectedRepairs.required' => 'Please select at least one repair solution.',
        ];
    }
}
