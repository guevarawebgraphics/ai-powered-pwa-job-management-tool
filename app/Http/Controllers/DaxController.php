<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Client;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\OpenAIFiles;
use App\Models\Machine;
use Illuminate\Support\Facades\Http;

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

    public function getVectorListings()
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.openai.api_key'),
            'OpenAI-Beta' => 'assistants=v2',
        ])->get('https://api.openai.com/v1/vector_stores');

        $json = $response->json();

        // Access first vector store
        $firstVectorStore = $json['data'] ?? null;

        return response()->json($firstVectorStore, $response->status());
    }

    public function updateVectorID(Request $request, $vectorID)
    {
        $validated = $request->validate([
            'name' => 'required|string',
        ]);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.openai.api_key'),
            'OpenAI-Beta' => 'assistants=v2',
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/vector_stores/' . $vectorID, [
            'name' => $validated['name']
        ]);

        return response()->json($response->json(), $response->status());
    }
    

    public function syncMachineFiles($modelNumber = null)
    {
        $files_uploaded = [];
        $environment_type = config('app.env');
        $machines = Machine::when($modelNumber, fn($q) => $q->where('model_number', $modelNumber))
            ->orderBy('created_at')
            ->get();

        foreach ($machines as $machine) {
            $current_folders = ['TechSheet', 'PartsList', 'ServicePointers'];

            $existingFiles = json_decode($machine->vector_files, true) ?? [];
            $existingFilenames = collect($existingFiles)->pluck('filename')->toArray();

            $newFilesToAttach = [];
            $allExpectedFilenames = [];


            if (!$machine->vector_id) {
                $create_response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . config('services.openai.api_key'),
                    'OpenAI-Beta'   => 'assistants=v2',
                    'Content-Type'  => 'application/json',
                ])->post('https://api.openai.com/v1/vector_stores', [
                    'name' => strtoupper($environment_type) . '-' . $machine->model_number,
                ]);

                // Step 3: Update Machine with dedicated Vector Store ID
                $vectorId = $create_response->json('id');
                $machine->update(['vector_id' => $vectorId]);
            } else {
                $vectorId = $machine->vector_id;
            }

            foreach ($current_folders as $folder) {
                $file_url = url(sprintf(
                    'api/machine-files/%s/%s/%s/%s',
                    $machine->machine_type ?? '',
                    $machine->brand_name ?? '',
                    $machine->model_number ?? '',
                    $folder
                ));

                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                ])->get($file_url);

                $fileArray = $response->json()['files'] ?? [];

                $destinationPath = public_path('cdn/pdfs/' . $machine->machine_type . '/' . $machine->brand_name . '/' . $machine->model_number . '/' . $folder);

                foreach ($fileArray as $file) {
                    $filename = $file['file_name'];
                    $allExpectedFilenames[] = $filename;

                    // Skip already uploaded files
                    if (in_array($filename, $existingFilenames)) {
                        continue;
                    }

                    $filePath = $destinationPath . '/' . $filename;

                    if (!file_exists($filePath)) {
                        \Log::info('File not found: ' . $filePath);
                        continue;
                    }

                    try {
                        $files_uploaded[] = $filename;

                        // Upload to OpenAI
                        $uploadResponse = Http::withToken(config('services.openai.api_key'))
                            ->attach('file', file_get_contents($filePath), $filename)
                            ->post('https://api.openai.com/v1/files', [
                                'purpose' => 'assistants',
                            ]);

                        $openaiFile = $uploadResponse->json();
                        $fileId = $openaiFile['id'];

                        // Attach to vector store
                        Http::withToken(config('services.openai.api_key'))
                            ->post("https://api.openai.com/v1/vector_stores/{$vectorId}/file_batches", [
                                'file_ids' => [$fileId],
                            ]);

                        $openaiFile['filename'] = $filename; // Add filename for tracking
                        $newFilesToAttach[] = $openaiFile;

                    } catch (\Exception $e) {
                        \Log::error("Upload error for $filename: " . $e->getMessage());
                    }
                }
            }

            // Delete files that are no longer expected
            foreach ($existingFiles as $fID) {
                if (!in_array($fID['filename'], $allExpectedFilenames)) {
                    try {
                        Http::withHeaders([
                            'Authorization' => 'Bearer ' . config('services.openai.api_key'),
                            'Accept' => '*/*',
                        ])->delete("https://api.openai.com/v1/files/" . $fID['id']);
                    } catch (\Exception $e) {
                        \Log::error("Delete error for file ID {$fID['id']}: " . $e->getMessage());
                    }
                }
            }

            // Merge only valid files: existing ones still present + new ones
            $validExistingFiles = array_filter($existingFiles, fn($f) => in_array($f['filename'], $allExpectedFilenames));
            $allFiles = array_merge($validExistingFiles, $newFilesToAttach);

            // Save to database
            $machine->update([
                'vector_files' => json_encode($allFiles),
            ]);
        }

        $updatedMachines = Machine::whereNotNull('vector_id')
            ->when($modelNumber, fn($q) => $q->where('model_number', $modelNumber))
            ->orderBy('created_at')
            ->get();


        return response()->json([
            'status'  => true,
            'message' => 'Vector ID successfully created and assigned to machines.',
            'data'    => [
                'files_uploaded'    =>  $files_uploaded
            ],
        ]);
    }

}
