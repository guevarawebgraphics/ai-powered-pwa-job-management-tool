<?php

namespace App\Services;

use App\Models\Chat;
use App\Models\User;
use App\Events\NewNotificationEvent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

use App\Notifications\FirebaseNotification;
use GuzzleHttp\Client as GuzzleClient;
use Google\Auth\Credentials\ServiceAccountCredentials;
use DB;

class ChatService
{
    public function store(array $data)
    {
        $user = User::find($data['from_user_id']);
        // $data['from_user_id']
        if ($data['role_id'] == 1) {
            $query = User::where('role_id', 0)->whereNull('deleted_at')->orderBy('created_at','DESC')->get();
          
            foreach ( $query ?? [] as $field ) {

                $input = [];
                $input['from_user_id'] = $data['from_user_id'];
                $input['to_user_id'] = $field->id;
                $input['message'] = $data['message'];
                $input['is_seen'] = 0;
                $store_query = Chat::create($input);


                     
                $notif_data = [
                    "name" => "New Message",
                    "content" => "A message has been sent you by " . $user->name . " (".$user->email.")." ,
                    "user_id" => $field->id,
                    "type" => 1,
                    "icon_type" => "fa-solid fa-comments",
                    "url"   =>  "#"
                ];
                
                // if (config('app.env') !== 'local') {
                    $notification_response = Http::withHeaders([
                        'Content-Type' => 'application/json',
                    ])->post(config('app.frontend_url').'/api/firebase/store', $notif_data);
                // }


            }
        } else if ($data['role_id'] == 0) {
            $input = [];
            $input['from_user_id'] = $data['from_user_id'];
            $input['to_user_id'] =  $data['to_user_id'];
            $input['message'] = $data['message'];
            $input['is_seen'] = 0;
            $store_query = Chat::create($input);

            $notif_data = [
                "name" => "New Message",
                "content" => "A message has been sent you by " . $user->name . " (".$user->email.")." ,
                "user_id" =>  $data['to_user_id'],
                "type" => 1,
                "icon_type" => "fa-solid fa-comments",
                "url"   =>  url('/profile')
            ];
            
            // if (config('app.env') !== 'local') {
               
                $this->callFirebaseNotification($notif_data);
            // }
        }
        

   
        return response()->json(['message' => 'Successfully stored - Administrator'], 201);
    }

    public function callFirebaseNotification($request) {

        // Expects Array Like this:
        // {
        //     "name":  "This is a test",
        //     "content":  "A new job has been added: [Job Title] at [Company Name]. Apply now before the deadline! 🚀",
        //     "user_id" :  3,
        //     "type":  1,
        //     "icon_type": "fas fa-tshirt"
        // }
        // Retrieve the title and body from the request
        $title = $request['name'];
        $body  = $request['content'];
        $user_id = $request['user_id'];
        $type = $request['type'];
        $icon_type = $request['icon_type'];
        $url = $request['url'] ?? url('/');

        // The root URL from your app configuration
        $root  = config('app.url');

        // Load Firebase credentials from a secure location (adjust the path as needed)
        $serviceAccountPath = '{
            "type": "service_account",
            "project_id": "appliance-repair-american",
            "private_key_id": "88d67482113ea4d8afd30edfeb81eae87fa37ac0",
            "private_key": "-----BEGIN PRIVATE KEY-----\nMIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQDIc5qMCSDh33US\nKzSyI/Y0bHU6aCPnoBvBgXk5VFJGSIf2cvWnSldtPAefPU7c1eqF/J8TxYn3k/2f\ngeCmZ7qAyncYWcHJOZuMiZ0XS3782vdd+XgOM6a1cKHfh6Yz/Idsn5XXt72RidOb\nYH6vQsT0nVdDEqAhTvqqeTcDGeKHBfA23JX4WSWcQpwoYfg3sXTYU3mp4ctcHKHT\nCjM8o3F6LRYQAKTOYf9rqAzrfoTCRsstER6TUSQNDcagfsNIDJbafmGe0mEp0n33\nqMPxUaa0oah7JJ5+++ZRxRwf+osg8m3tHoLU/BHH9VXeQE8CKkpXUdzMmUkD9kkm\nasbTbXR7AgMBAAECggEANz1odzPizGxEunOOq5LufAbxjV16m4/LZlm0WwCqRbUi\nRt/qY6ZAjplO7mq6S3BmF3OFMTEscr5lU+o6SxFl260wAdVmanpGLAYWdP94qNz8\n83hWxe8k/3ZhHZUnz8bFWUFnTVHU24E0f+qpLspDNbgQfqro1xUJTiZmayPcgeG7\nkakH/0UntP49k05aRQWkEGjItC9fkWv8G0NQRsMFtq8XV6lwgXsjX8CismxTjBFZ\n9Y+rL8PJtXwcL0C0R6jE+t0DHcDJljfoOFCnfKMHblrR3w77I7U11x45cWeTKh8o\nqAspf+3nSIoyb2bT/4jUjlWyEUwkpSftlD2j+p443QKBgQDtaG2eC/NSDPDqvrF/\nOW5jQiosCfjKxmlPp5V3CvEJlmglWeUoQlBpa9mPTMWREcnhitKI6x3OeM75UU2O\ncHjYBAokiGi5ZBfaIP5v7xCjccWRqvYQIlFCs8TXOy2QRHQQLG7SK5TMop2ygwCd\nIJClhogJi+4UBC167E9zPhQpvwKBgQDYJkWcpFbQ24Prb5KkIqRuwzBlX+WMFGgC\n0vN4MMV08QvrSwdSH65ERvoe6zcbKPdaLmEwNePWJx/4SeKqI4CoTFnqrgvxQNl8\nGNHkaICHL4WkaXJUPIcfwOXgDAAQawWCgvc1EoCPxp0oXoppFSJgq0AifRW+dcIl\ns/g2g8bMRQKBgHS++cjSz6D1xDNr+xU5RNOC4gIuFS6CS59kJdgYhVfVovlbL5mQ\nosJ0ytPxSOaB/Ojwv3+dP1cdiYqJNL5rCMIRmntEqVshoakeuICK63+0nxd3HvHo\nLlXC2XWOIGXlg73AW+72h4HqJep3CYI7VWZQte0b5sPpGgRs2NUvHfM5AoGBANIP\np7lKsSB7JWKKZP3kuN74DLGIl+Ih9s8/yeO1qkAy/n1lxjCWn1q1i+C+gSiBEJpw\n1nzu8oQuM9CtpX3p0OA+i2gaS3rjpjkHOfx7XuWCVqPo4nZg+ITwh7bMksdh6tHi\ni3Llj1GrGRKhhmwHYQiHhWW/IIGfmdXgaEo6mNntAoGAMyE95EBNhN5RGuvfjUCd\n35FZBHgL3eSQLqb0t4TbqAh4DX8zr5Al0sG1TrG28I1PK6/v7YqD8D9VSOkPB8vU\nbSHcoopUlyAO4HY/ePaEd6hw43FaTyI5jMQTh+Xx1iU/sgQYU3JCxRQhtaGrRMzN\ncEbVqdlXZ01dyP8887Wny9w=\n-----END PRIVATE KEY-----\n",
            "client_email": "firebase-adminsdk-fbsvc@appliance-repair-american.iam.gserviceaccount.com",
            "client_id": "108195718235646590135",
            "auth_uri": "https://accounts.google.com/o/oauth2/auth",
            "token_uri": "https://oauth2.googleapis.com/token",
            "auth_provider_x509_cert_url": "https://www.googleapis.com/oauth2/v1/certs",
            "client_x509_cert_url": "https://www.googleapis.com/robot/v1/metadata/x509/firebase-adminsdk-fbsvc%40appliance-repair-american.iam.gserviceaccount.com",
            "universe_domain": "googleapis.com"
        }';
        $credentials = json_decode($serviceAccountPath, true);

        $scopes = ["https://www.googleapis.com/auth/firebase.messaging"];
        // Pass $scopes first, then $credentials:
        $serviceAccount = new ServiceAccountCredentials($scopes, $credentials);
        $tokenInfo = $serviceAccount->fetchAuthToken();
        $accessToken = $tokenInfo['access_token'] ?? null;

        if (!$accessToken) {
            return response()->json(['error' => 'Could not fetch access token'], 500);
        }

        // Retrieve distinct device tokens from the database (adjust table/model as needed)
        // $tokens = DB::table('users')->distinct()->pluck('device_token')->toArray();
        $tokens = DB::table('users')
        ->where('id', $user_id)
        ->pluck('device_token')
        ->toArray();

        // Filter out any invalid tokens
        $tokens = array_filter($tokens, function($token) {
            return !empty($token);
        });


        // Instantiate a Guzzle HTTP client
        $client = new GuzzleClient();

        // Your FCM endpoint - update your project id accordingly
        $fcmUrl = "https://fcm.googleapis.com/v1/projects/appliance-repair-american/messages:send";

        // Loop through tokens and send notifications
        foreach ($tokens as $deviceToken) {

            if (empty($deviceToken)) {
                
                \Log::error($tokens);
                \Log::error("Empty device token found. Skipping." . $deviceToken);
                continue;
            }
            
            $payload = [
                "message" => [
                    "token" => $deviceToken,
                    "notification" => [
                        "title" => $title,
                        "body"  => $body,
                        // "image" => $root . '/images/icons/android-icon-192x192.png',
                    ],
                    "data" => [
                        "type" => (string) $type, // Custom Field
                        "icon_type" => (string) $icon_type, // Custom Field
                        "url" => (string) $url, // Custom Field
                    ],
                    "android" => [
                        "notification" => [
                            "icon" => "ARA"
                        ]
                    ],
                    "webpush" => [
                        "fcm_options" => [
                            "link" => $root
                        ]
                    ]
                ]
            ];

            try {
                $client->post($fcmUrl, [
                    'headers' => [
                        'Content-Type'  => 'application/json',
                        'Authorization' => 'Bearer ' . $accessToken
                    ],
                    'json' => $payload,
                ]);

                \Log::info(json_encode($client));
            } catch (\Exception $e) {
                // Log error and continue sending to other tokens
                \Log::error("FCM error for token {$deviceToken}: " . $e->getMessage());
            }
        }

        return response()->json(['message' => 'Notification has been sent']);
       
    }
}
