<?php

namespace App\Services;

use App\Models\User;
use App\Models\Gig;
use App\Events\NewNotificationEvent;
use Illuminate\Support\Facades\Log;

class GigService
{
    public function update(array $data)
    {
        
        $data = Gig::where('gig_id', $data['gig_id'])->update([
            'resolution'    =>  $data['resolution'],
            'gig_report_images'    =>  $data['gig_report_images'],
            'gig_complete'  =>  3, // Submitted Report,
            'addtl_recommended_repairs' =>  $data['addtl_recommended_repairs']
        ]);

        return response()->json([
            'message' => 'Successfully stored report!', 
            'data'    =>  $data
        ], 201);
    }

}
