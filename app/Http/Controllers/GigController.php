<?php

namespace App\Http\Controllers;

use App\Requests\Gig\StoreRequest;
use App\Services\GigService;
use Illuminate\Http\Request;

class GigController extends Controller
{    
    public function __construct(GigService $gigService)
    {
        $this->gigService = $gigService;
    }
    public function update(StoreRequest $request)
    {
        $input =  $request->all();

            // Build the JSON format
        $json = [
            [
                "jobCompletion" => $request->type, // "diagnostic" or "full-repair"
                "solution" => $request->selectedRepairs, // Selected repairs array
                "partsUsed" => $request->selectedParts, // Selected parts array,
                "added_common_repair"   =>  []
            ]
        ];

        // Store formatted JSON in the input array
        $input['resolution'] = json_encode($json);

        // ✅ Store images and collect paths
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $fileName = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                $filePath = "/images/gigs/" . $fileName;
                
                // Move file to the desired location
                $image->move(public_path('images/gigs'), $fileName);

                // Store the path
                $imagePaths[] = $filePath;
            }
        }

        // ✅ Store image paths as JSON
        $input['gig_report_images'] = json_encode($imagePaths);

        $query = $this->gigService->update( $input );

        return response()->json([
            'message' => 'Successfully stored report!',
            'user' => $query,
        ], 201);
    }
}
