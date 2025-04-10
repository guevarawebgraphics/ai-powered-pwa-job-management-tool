<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index($techID)
    {
        $query = [
            'gig_report'    =>  [

            ],
            'job_time_report'    =>  [

            ],
            'revenue_report'    =>  [
                
            ],
            'fastest_gig'   =>  '12 minutes',
            'most_valuable_gig' =>  105,
            'most_gigs_in_a_day'    =>  7,
            'average_daily_earn'    =>  220
        ];

        return response()->json([
            'message' => 'Successfully retrieved analytic report!',
            'data' => $query,
        ], 201);
    }
}
