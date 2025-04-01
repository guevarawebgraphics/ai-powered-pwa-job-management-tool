<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserService
{
    public function update(array $data)
    {
        $user = auth()->user(); // Get authenticated user

        \Log::info('Received Data:', $data); // Debugging

        // Handle profile photo upload
        if (request()->hasFile('profile_photo')) {
            $file = request()->file('profile_photo');
            $filename = time() . '_' . $file->getClientOriginalName(); // Unique filename
            $file->move(public_path('images/profile_photo'), $filename); // Move to /public/images/profile_photo
            $data['profile_photo'] = url("images/profile_photo/{$filename}"); // Generate accessible URL
        }


        $user->update([
            'name' => $data['name'] ?? $user->name,
            'professional_title' => $data['professional_title'] ?? $user->professional_title,
            'profile_photo' => $data['profile_photo'] ?? $user->profile_photo,
            'mobile_no' => $data['mobile_no'] ?? $user->mobile_no,
            'home_no' => $data['home_no'] ?? $user->home_no,
            'service_area' => $data['service_area'] ?? $user->service_area,
            'skills' => isset($data['skills']) ? json_encode($data['skills']) : $user->skills,
            'current_address' => $data['current_address'] ?? $user->current_address,
            'is_notify' => isset($data['is_notify']) ? ($data['is_notify'] == "1" ? "1" : "0") : $user->is_notify,
            'is_location' => isset($data['is_location']) ? ($data['is_location'] == "1" ? "1" : "0") : $user->is_location,
            'skills' => isset($data['skills']) ? implode(',', $data['skills']) : $user->skills,
            'black_out_from'    => isset($data['black_out_from']) ?  $data['black_out_from'] : $user->black_out_from,
            'black_out_to'    => isset($data['black_out_to']) ?  $data['black_out_to'] : $user->black_out_to,
            'is_blackout' => isset($data['is_blackout']) ? ($data['is_blackout'] == "1" ? "1" : "0") : $user->is_blackout,

        ]);

        return response()->json($user); // Return updated user data
    }


}