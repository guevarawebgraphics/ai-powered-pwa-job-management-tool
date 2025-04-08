<?php

namespace App\Http\Controllers;

use App\Requests\Gig\StoreRequest;
use App\Services\GigService;
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use App\Models\Gig;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use GuzzleHttp\Client as GClient;

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

        // ✅ Process Additional Recommended Repairs Images
        if (!empty($request->addtl_recommended_repairs)) {
            $addtlRepairs = json_decode($request->addtl_recommended_repairs, true);

            foreach ($addtlRepairs as &$repair) {
                if (!empty($repair['images'])) {
                    foreach ($repair['images'] as &$image) {
                        $imageUrl = $image['url'];

                        // ✅ Fetch the image using Laravel's HTTP client
                        $response = Http::get($imageUrl);

                        if ($response->successful()) {
                            // Generate new filename
                            $fileName = time() . '-' . uniqid() . '.' . pathinfo($imageUrl, PATHINFO_EXTENSION);
                            $filePath = public_path("images/gigs/" . $fileName);

                            // ✅ Ensure the directory exists
                            File::ensureDirectoryExists(public_path('images/gigs'));

                            // ✅ Save the image in /public/images/gigs/
                            File::put($filePath, $response->body());

                            // Update image URL
                            $image['url'] = "/images/gigs/" . $fileName;
                            $image['filename'] = $fileName;
                        }
                    }
                }
            }
            $input['addtl_recommended_repairs'] = json_encode($addtlRepairs);
        } else {
            $input['addtl_recommended_repairs'] = NULL;
        }



        
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

    public function getGigsPerMachine($modelNumber) {
        $query = Gig::where('model_number', $modelNumber)->whereNull('deleted_at')->orderBy('created_at','DESC')->get();
        return $query;
    }

    public function getTravelTime(Request $request) {
        $lat = $request->input('lat');
        $lng = $request->input('lng');
        $destination = $request->input('destination');
        $apiKey = config('services.google.map.key'); // Make sure this is defined in your .env file

        // Build the URL and query parameters
        $url = 'https://maps.googleapis.com/maps/api/distancematrix/json';
        $params = [
            'units'      => 'metric',
            'origins'    => "{$lat},{$lng}",
            'destinations' => $destination,
            'key'        => $apiKey,
        ];

        try {
            $client = new GClient();
            $response = $client->request('GET', $url, ['query' => $params]);
            $data = json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            // Optionally log the error or handle it as you see fit
            return response()->json(['error' => 'Error fetching travel time'], 500);
        }

        return response()->json($data);
    }
}
