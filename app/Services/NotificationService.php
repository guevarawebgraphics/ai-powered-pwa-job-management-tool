<?php

namespace App\Services;

use App\Models\Notification;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function store(array $data)
    {
        $query = Notification::create($data);
        
        return response()->json(['message' => 'Successfully stored!'], 201);
    }

    public function update(array $data, int $notifyID)
    {
        $query = Notification::where('id', $notifyID)->update($data);
        
        return response()->json(['message' => 'Successfully stored!'], 201);
    }
}
