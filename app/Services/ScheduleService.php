<?php

namespace App\Services;

use App\Models\SchedulePerUser;
use Illuminate\Support\Facades\Log;

class ScheduleService
{
    // Mapping days to numbers
    private $dayMapping = [
        'Monday' => 1,
        'Tuesday' => 2,
        'Wednesday' => 3,
        'Thursday' => 4,
        'Friday' => 5,
        'Saturday' => 6,
        'Sunday' => 7,
    ];

    public function store(array $data)
    {
        Log::info($data); // Log incoming data for debugging

        $userId = auth()->user()->id; // Get the authenticated user ID

        foreach ($data as $daySchedule) {
            // Convert day name to its respective number
            $dayNumber = $this->dayMapping[$daySchedule['name']] ?? null;

            if (!$dayNumber) {
                Log::error("Invalid day name: " . $daySchedule['name']);
                continue; // Skip invalid days
            }

            if ($daySchedule['closed']) {
                // If the day is closed, remove all existing open slots for the user & day
                SchedulePerUser::where('day', $dayNumber)
                    ->where('user_id', $userId)
                    ->where('is_close', 0) // Remove only open slots
                    ->delete();

                // Store/update the closed status
                SchedulePerUser::updateOrCreate(
                    [
                        'day' => $dayNumber,
                        'user_id' => $userId,
                    ],
                    [
                        'is_close' => true,
                        'open' => null,
                        'close' => null,
                    ]
                );
            } else {
                // If the day is open, remove the closed record (if exists)
                SchedulePerUser::where('day', $dayNumber)
                    ->where('user_id', $userId)
                    ->where('is_close', 1) // Remove only closed record
                    ->delete();

                // Loop through each slot and store/update it
                foreach ($daySchedule['slots'] as $slot) {
                    SchedulePerUser::updateOrCreate(
                        [
                            'day' => $dayNumber,
                            'user_id' => $userId,
                            'open' => $slot['open'], // Ensure matching time slot
                            'close' => $slot['close']
                        ],
                        [
                            'is_close' => false,
                        ]
                    );
                }
            }
        }

        return response()->json(['message' => 'Schedule saved successfully'], 201);
    }
}
