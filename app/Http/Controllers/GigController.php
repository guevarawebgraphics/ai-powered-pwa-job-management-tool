<?php

namespace App\Http\Controllers;

use App\Requests\Gig\StoreRequest;
use App\Services\GigService;
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use App\Models\Gig;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            ]
        ];

        // Store formatted JSON in the input array
        $input['resolution'] = json_encode($json);

        $recommendedRepairs = json_decode($request->addtl_recommended_repairs, true) ?? [];
        

        foreach ($recommendedRepairs as $repairIndex => &$repairItem) { // Use reference '&' to modify directly
            $updatedImages = []; // Store the correct image paths
            
            
            // Check if images exist in the request
            if ($request->hasFile("addtl_recommended_repairs_images.$repairIndex")) {
                foreach ($request->file("addtl_recommended_repairs_images.$repairIndex") as $repairImage) {
                    $xfileName = time() . '-' . uniqid() . '.' . $repairImage->getClientOriginalExtension();
                    $xfilePath = "/images/gigs/" . $xfileName;
                    
                    // Move file to the desired location
                    $repairImage->move(public_path('images/gigs'), $xfileName);
                    // Store the path
                    $updatedImages[] = $xfilePath;
                }
            }

            \Log::info($updatedImages);

            // Update the images field
            $repairItem['images'] = $updatedImages;
        }

        // Store back as JSON
        $input['addtl_recommended_repairs'] = json_encode($recommendedRepairs);



        
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
            'user' => $input,
        ], 201);
    }

    public function indexQuickHistory($clientId)
    {
        $query = \DB::table('gigs')->where('client_id', $clientId)->select('gig_id','gig_cryptic', 'gig_price')->orderBy('created_at','DESC')->get();
        \Log::info( $query );
        return response()->json([
            'message' => 'Successfully retrieved quick history!',
            'data' => $query,
        ], 201);
    }

    public function sendGigSMS(Request $request) {

        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $twilio = new Client($sid, $token);
        $type = $request->type;
        $gig_id = $request->gig_id;
        $gig = Gig::with(['client','technician'])->where('gig_id', $gig_id)->first();

        // These are the type of SMS Sent to this request.
        // arriving-early
        // on-time
        // behind-schedule

        $message = "This is Appliance Repair American Test SMS. Send email to info@guevarawebgraphics.com if you received this message. Thank you!";

        if ($type == "arriving-early") {
            $message = "Hello ".$gig->client->client_name.", This is Mark with Appliance Repair American. I am ahead of schedule. I can be at your location in 'pull time from GPS' Is this Ok?";
        } else if ($type == "on-time") {

            $message = "Hello ".$gig->client->client_name.", This is " . $gig->technician->name. " with Appliance Repair American. I will be arriving close to 'pull time from GPS'. Are you at the location?";
        } else if ($type == "behind-schedule") {
            
            $message =  "Hello ".$gig->client->client_name.", This is " . $gig->technician->name. " with Appliance Repair American. I am behind schecdule. I appologize for the delay. Dont hesitate to message me if you have any questions";
        } 

        $response = $twilio->messages->create(
            "+17729132268", // To
            [
                "body" => $message,
                "from" => "+17726778837",
            ]
        );

        \Log::info('Start Twilio API Response');
        \Log::info($response);
        \Log::info('End Twilio API Response');

        return response()->json([
            'message' => 'Successfully sent SMS',
            'data' => $response,
        ], 201);
    }

    public function storeTemporaryImage(Request $request) {
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('uploads', $filename, 'public'); // Store in storage/app/public/uploads

            return response()->json([
                'url' => asset('storage/uploads/' . $filename),
                'filename' => $filename // Send back filename for future deletion
            ]);
        }
        return response()->json(['error' => 'No file uploaded'], 400);
    }

    public function destroyTemporaryImage(Request $request) {
        $filename = $request->filename;

        if ($filename && Storage::disk('public')->exists("uploads/$filename")) {
            Storage::disk('public')->delete("uploads/$filename");
            return response()->json(['message' => 'Image deleted successfully']);
        }

        return response()->json(['error' => 'File not found'], 404);
    }
}
