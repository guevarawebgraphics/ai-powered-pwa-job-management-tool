<?php

namespace App\Http\Controllers;

use App\Requests\Gig\StoreRequest;
use App\Services\GigService;
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use App\Models\Gig;

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
}
