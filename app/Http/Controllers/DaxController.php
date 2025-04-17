<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Client;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
USE App\Models\OpenAIFiles;

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
    public function voiceChat(Request $request)
    {
        try {
            // Check if the request has an audio file
            if (!$request->hasFile('audio')) {
                return response()->json(['error' => 'No audio file found in request.'], 422);
            }

            $file = $request->file('audio');

            if (!$file->isValid()) {
                return response()->json(['error' => 'Uploaded file is invalid.'], 400);
            }

            Log::info('Request files:', $request->all());

            // Ensure the 'temp' directory exists in the local disk
            if (!Storage::disk('local')->exists('temp')) {
                Storage::disk('local')->makeDirectory('temp');
            }

            // Generate a unique file name with a .webm extension and store the file
            $uniqueName = uniqid('voice_') . '.webm';
            $path = Storage::disk('local')->putFileAs('temp', $file, $uniqueName);
            $fullPath = storage_path('app/' . $path);

            // Log file path and existence using Laravel Storage
            Log::info('Audio file path', [
                'path'     => $path,
                'fullPath' => $fullPath,
                'exists'   => Storage::disk('local')->exists($path),
            ]);

            if (!Storage::disk('local')->exists($path)) {
                return response()->json(['error' => 'Stored audio file not found on server.'], 500);
            }

            // Transcribe the audio using OpenAI Whisper
            $transcription = OpenAI::audio()->transcribe([
                'file'  => fopen($fullPath, 'r'),
                'model' => 'whisper-1',
            ]);

            $transcript = $transcription['text'] ?? '';

            // Send the transcript to GPT-4 for a reply
            $chatResponse = OpenAI::chat()->create([
                'model'    => 'gpt-4',
                'messages' => [
                    ['role' => 'user', 'content' => $transcript],
                ],
            ]);

            $reply = $chatResponse->choices[0]->message->content ?? '';

            // Convert the reply to audio using OpenAI TTS
            $tts = OpenAI::audio()->speech([
                'model'           => 'tts-1',
                'input'           => $reply,
                'voice'           => 'nova', // options: nova, shimmer, alloy, etc.
                'response_format' => 'mp3',
            ]);

            // Save the MP3 TTS output to public storage
            $filename = 'tts_' . uniqid() . '.mp3';
            Storage::disk('public')->put("tts/$filename", $tts);

            return response()->json([
                'reply'     => $reply,
                'audio_url' => asset("storage/tts/$filename"),
            ]);
        } catch (\Throwable $e) {
            Log::error('Voice Chat Error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
    }
    public function getFiles()
    {
        $fileIds = OpenAIFiles::whereNull('deleted_at')
            ->whereNotNull('file_id') // make sure file_id exists
            ->orderBy('created_at')
            ->pluck('file_id'); // just the file_id column

        return response()->json($fileIds->toArray());
    }
}
