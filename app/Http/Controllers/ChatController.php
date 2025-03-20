<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Chat;
use App\Requests\Notification\StoreRequest;
use App\Services\NotificationService;
use App\Services\ChatService;
use App\Requests\Chat\StoreRequest As ChatStoreRequest;
use Illuminate\Support\Facades\Event;
use App\Events\NewNotificationEvent;

class ChatController extends Controller
{
    public function __construct(NotificationService $notificationService, ChatService $chatService)
    {
        $this->chatService = $chatService;
        
        $this->notificationService = $notificationService;
    }


    public function index(){
        $query = Chat::with(['sender','receiver'])->where('from_user_id', auth()->user()->id )->orWhere('to_user_id', auth()->user()->id)->whereNull('deleted_at')->orderBy('created_at','asc')->get();
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
