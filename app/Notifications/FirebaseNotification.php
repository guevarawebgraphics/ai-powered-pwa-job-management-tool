<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class FirebaseNotification extends Notification
{
    protected $deviceToken;
    protected $notificationData;

    public function __construct($deviceToken, array $notificationData)
    {
        $this->deviceToken = $deviceToken;
        $this->notificationData = $notificationData;
    }

    // Instead of 'via', you can define your own method
    public function send()
    {
        $serverKey = env('FCM_SERVER_KEY');
    
        // Ensure device token is a string
        $token = is_array($this->deviceToken) ? $this->deviceToken[0] : $this->deviceToken;

        $payload = [
            'to' => $token,
            'notification' => [
                'title' => $this->notificationData['title'],
                'body'  => $this->notificationData['body']
            ],
            'data' => $this->notificationData['data'] ?? []
        ];

        $response = Http::withHeaders([
            'Authorization' => 'key=' . $serverKey,
            'Content-Type'  => 'application/json',
        ])->post('https://fcm.googleapis.com/v1/projects/appliance-repair-american/messages:send', $payload);

        \Log::info('FCM response:', [$response->body()]);
        return $response;
    }
}