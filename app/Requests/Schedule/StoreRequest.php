<?php

namespace App\Requests\Schedule;

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
            'schedule'                  => 'required|array',
            'schedule.*.name'           => 'required|string', 
            'schedule.*.closed'         => 'required|boolean',
            'schedule.*.slots'          => 'required_if:schedule.*.closed,false|array',
            
            // Validate each slot when the day is not closed
            'schedule.*.slots.*.open'   => 'required_if:schedule.*.closed,false',
            'schedule.*.slots.*.close'  => 'required_if:schedule.*.closed,false',
        ];
    }

    /**
     * Custom validation error messages.
     */
    public function messages()
    {
        return [
            'schedule.required'                 => 'The schedule data is required.',
            'schedule.array'                    => 'The schedule must be an array.',

            'schedule.*.name.required'          => 'Each day must have a name.',
            'schedule.*.name.string'            => 'The day name must be a string.',

            'schedule.*.closed.required'        => 'The closed status for each day is required.',
            'schedule.*.closed.boolean'         => 'The closed status must be true or false.',

            'schedule.*.slots.required_if'      => 'Time slots are required when the day is open.',

            'schedule.*.slots.*.open.required_if'  => 'The opening time is required when the day is open.',
            'schedule.*.slots.*.open.string'       => 'The opening time must be a valid string.',

            'schedule.*.slots.*.close.required_if' => 'The closing time is required when the day is open.',
            'schedule.*.slots.*.close.string'      => 'The closing time must be a valid string.',
        ];
    }

    /**
     * Custom attribute names for better readability in validation errors.
     */
    public function attributes()
    {
        return [
            'schedule'                  => 'Schedule Data',
            'schedule.*.name'           => 'Day Name',
            'schedule.*.closed'         => 'Closed Status',
            'schedule.*.slots'          => 'Time Slots',
            'schedule.*.slots.*.open'   => 'Opening Time',
            'schedule.*.slots.*.close'  => 'Closing Time',
        ];
    }
}
