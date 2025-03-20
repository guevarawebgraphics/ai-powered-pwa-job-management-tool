<?php

namespace App\Services;

use App\Models\Chat;
use Illuminate\Support\Facades\Log;

class ChatService
{
    public function store(array $data)
    {

        $data['from_user_id']   =  auth()->user()->id;
        $query = Chat::create($data);
        
        return response()->json(['message' => 'Successfully stored!'], 201);
    }

}
