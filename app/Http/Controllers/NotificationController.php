<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Requests\Notification\StoreRequest;
use App\Requests\Notification\UpdateRequest;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Event;
use App\Events\NewNotificationEvent;
use App\Events\SeenNotificationEvent;
use Twilio\Rest\Client;

class NotificationController extends Controller
{
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index($userId, $notifyType){
        $query = Notification::where('user_id', $userId)->where('type', $notifyType)->whereNull('deleted_at')->orderBy('created_at','DESC')->get();
        return response()->json([
            'message' => 'Successfully retrieved notifications!',
            'data' => $query,
        ], 201);
    }


    public function store(StoreRequest $request)
    {

        // This request expects

        // {
        //     "name":  "This is a test",
        //     "content":  "A new job has been added: [Job Title] at [Company Name]. Apply now before the deadline! 🚀",
        //     "user_id" :  3,
        //     "type":  1,
        //     "icon_type": "fas fa-tshirt"
        // }
        
        $query = $this->notificationService->store( $request->all() );

        // Push Notifications
        if (config('app.env') == "local") {
            event(new NewNotificationEvent($request->all()));
        }

        // SMS Notifications
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $twilio = new Client($sid, $token);

        $response = $twilio->messages->create(
            "+17729132268", // To
            [
                "body" => $request->content,
                "from" => "+17726778837",
            ]
        );

        \Log::info('Start Twilio API Response');
        \Log::info($response);
        \Log::info('End Twilio API Response');



        return response()->json([
            'message' => 'Successfully stored!',
            'user' => $query,
        ], 201);
    }

    public function update(UpdateRequest $request, $notifyID)
    {
        $query = $this->notificationService->update( $request->except(['id','user_id']), $notifyID );

        $notify = Notification::find($notifyID);

        if (config('app.env') == "local") {
            // event(new NewNotificationEvent($notify->toArray()));
            
            event(new SeenNotificationEvent($notify->toArray()));
        }

        return response()->json([
            'message' => 'Successfully updated!',
            'user' => $notify,
        ], 201);
    }

    public function getUnseen($userId)
    {
        $notify = Notification::where('user_id', $userId)->where('is_seen', 0)->whereNull('deleted_at')->count();
        return response()->json([
            'message' => 'Successfully retrieve!',
            'count' => $notify,
        ], 201);
    }
}
