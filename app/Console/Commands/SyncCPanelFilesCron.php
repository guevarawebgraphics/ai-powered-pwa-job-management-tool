<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Gig;
use App\Models\Machine;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Log;

class SyncCPanelFilesCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-cpanel-files-cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will run syncing of cpanel files to openai';

    /**
     * Execute the console command.
     */
    // public function handle()
    // {
    //     $modelNumber = '';
    //     // Step 1: Fetch machines without vector_id
    //     // $machines = Machine::whereNull('vector_id')->when($modelNumber, fn($q) => $q->where('model_number', $modelNumber))
    //     $machines = Machine::when($modelNumber, fn($q) => $q->where('model_number', $modelNumber))
    //         ->orderBy('created_at')
    //         ->get();

    //     foreach ($machines ?? [] as $machine) {

    //         $current_folders = [
    //             'TechSheet',
    //             'PartsList',
    //             'ServicePointers'
    //         ];

    //         // Step 1: Delete Vector
    //         if ($machine->vector_id) {
    //             $delete_response = Http::withHeaders([
    //                 'Authorization' => 'Bearer ' . config('services.openai.api_key'),
    //                 'OpenAI-Beta' => 'assistants=v2',
    //             ])->delete("https://api.openai.com/v1/vector_stores/{$machine->vector_id}");
    //         }

    //         // Step 2: Create Vector
    //         if (!$machine->vector_id) {
    //             $create_response = Http::withHeaders([
    //                 'Authorization' => 'Bearer ' . config('services.openai.api_key'),
    //                 'OpenAI-Beta'   => 'assistants=v2',
    //                 'Content-Type'  => 'application/json',
    //             ])->post('https://api.openai.com/v1/vector_stores', [
    //                 'name' => $machine->model_number,
    //             ]);

    //             // Step 3: Update Machine with dedicated Vector Store ID
    //             $vectorId = $create_response->json('id');
    //             $machine->update(['vector_id' => $vectorId]);
    //         } else {
    //             $vectorId = $machine->vector_id;
    //         }

    //         $file_id_array = json_decode($machine->vector_files) ?? []; 
    //         foreach($file_id_array ?? [] as $fID) {
    //             $delete_response = Http::withHeaders([
    //                 'Authorization' => 'Bearer ' . config('services.openai.api_key'),
    //                 'Accept' => '*/*',
    //             ])->delete("https://api.openai.com/v1/files/". $fID->id);
    //         }




    //         $fileIdArray = [];

    //         foreach ($current_folders ?? [] as $folder ) {

    //             // Step 4: Prepare PDF Files
    //             $file_url = url(sprintf(
    //                 'api/machine-files/%s/%s/%s/%s',
    //                 $machine->machine_type ?? '',
    //                 $machine->brand_name ?? '',
    //                 $machine->model_number ?? '',
    //                 $folder ?? ''
    //             ));

    //             $response = Http::withHeaders([
    //                 'Content-Type' => 'application/json',
    //             ])->get($file_url);

    //             $fileArray = $response->json()['files'] ?? [];

    //             $destinationPath = public_path('cdn/pdfs/' . $machine->machine_type .'/' . $machine->brand_name . '/' . $machine->model_number . '/' . $folder );

    //             foreach ($fileArray ?? [] as $file ) {

    //                 // Step 5: Uplaod files to OPENAI Server

    //                 $filename = $file['file_name'];
    //                 $uploadFileOpenai = Http::withToken(config('services.openai.api_key'))
    //                 ->attach('file', file_get_contents($destinationPath . '/' . $filename), $filename)
    //                 ->post('https://api.openai.com/v1/files', [
    //                     'purpose' => 'assistants'
    //                 ]);


    //                 $openaiFile = $uploadFileOpenai->json();

    //                 $fileId = $openaiFile['id'];

    //                 // Step 6: Attach file to Vector Stode ID
    //                 $attachResponse = Http::withToken(config('services.openai.api_key'))
    //                 ->post("https://api.openai.com/v1/vector_stores/{$vectorId}/file_batches", [
    //                     'file_ids' => [$fileId]
    //                 ]);

    //                 $openaiVector = $attachResponse->json();

    //                 array_push($fileIdArray, $openaiFile);


    //             }

    //         }
            
    //         $machine->update(['vector_files' => json_encode($fileIdArray)]);
            
    //     }

    //     // Step 2: Return updated machines
    //     $updatedMachines = Machine::whereNotNull('vector_id')
    //         ->when($modelNumber, fn($q) => $q->where('model_number', $modelNumber))
    //         ->orderBy('created_at')
    //         ->get();

            
    //     $this->info(json_encode([
    //         'status'  => true,
    //         'message' => 'Vector ID successfully created and assigned to machines.',
    //         'data'    => $updatedMachines,
    //     ]));
    // }


    public function handle()
    {
        $modelNumber = '';
        $environment_type = config('app.env');
        $files_uploaded = [];
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

        $this->info(json_encode([
            'status' => true,
            'message' => 'Vector ID sync completed.',
            'data' => [
                'machines' => $updatedMachines,
                'files_uploaded' => $files_uploaded,
            ],
        ]));
    }

}
