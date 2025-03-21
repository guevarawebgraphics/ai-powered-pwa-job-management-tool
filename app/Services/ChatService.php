<?php

namespace App\Services;

use App\Models\Chat;
use App\Models\User;
use App\Events\NewNotificationEvent;
use Illuminate\Support\Facades\Log;

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
                    "icon_type" => "fa-solid fa-comments"
                ];
                
                if (config('app.env') == "local") {
                    event(new NewNotificationEvent($notif_data));
                }

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
                "icon_type" => "fa-solid fa-comments"
            ];
            
            if (config('app.env') == "local") {
                event(new NewNotificationEvent($notif_data));
            }
        }
        

   
        return response()->json(['message' => 'Successfully stored!'], 201);
    }

}
