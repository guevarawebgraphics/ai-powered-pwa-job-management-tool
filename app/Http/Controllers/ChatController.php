<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\User;
use App\Requests\Notification\StoreRequest;
use App\Services\NotificationService;
use App\Services\ChatService;
use App\Requests\Chat\StoreRequest As ChatStoreRequest;
use Illuminate\Support\Facades\Event;
use App\Events\NewNotificationEvent;
use App\Notifications\FirebaseNotification;
use GuzzleHttp\Client;
use Google\Auth\Credentials\ServiceAccountCredentials;
use DB;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    public function __construct(NotificationService $notificationService, ChatService $chatService)
    {
        $this->chatService = $chatService;
        
        $this->notificationService = $notificationService;
    }

    public function index(Request $request){
        $input = $request->all();
        $fromUserId = $input['from_user_id'];

        $query = Chat::with(['sender', 'receiver'])
        ->whereIn('id', function ($subquery) use ($fromUserId) {
            $subquery->selectRaw('MAX(id)')
                ->from('chats')
                ->where(function ($q) use ($fromUserId) {
                    $q->where('from_user_id', $fromUserId)
                    ->orWhere('to_user_id', $fromUserId);
                })
                ->whereNull('deleted_at')
                ->groupBy('from_user_id','created_at');
        })
        ->orderBy('created_at', 'asc') // Ordering the latest messages
        ->get();


        return response()->json([
            'message' => 'Successfully retrieved chats!',
            'data' => $query,
        ], 201);
    }


    public function store(ChatStoreRequest $request)
    {
        
        $query = $this->chatService->store( $request->all() );

        return response()->json([
            'message' => 'Successfully stored!',
            'user' => $query,
        ], 201);
    }

}
