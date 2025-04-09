<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Client;

class DaxController extends Controller
{
    public function chat(Request $request){

        try {

            $messages = $request->input('messages');

            $response = OpenAI::chat()->create([
                'model' => 'gpt-4', // or 'gpt-3.5-turbo' for cheaper testing
                'messages' => $messages,
            ]);

            return response()->json([
                'reply' => $response->choices[0]->message->content // ✅ correct
            ]);

        } catch (\Throwable $e)  {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
        
    }
}
