<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Gig;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Log;

class NotificationCronJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:notification-cron-job';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will run notification for both SMS & Push.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info('Custom cron job executed at ' . now());

        // Get gigs where start_datetime has passed by at least 1 hour
        // $gigs = Gig::with(['client','technician','machine'])->where('start_datetime', '<', Carbon::now()->subHour()) // Gig started at least 1 hour ago
        //     ->orderBy('start_datetime', 'ASC')
        //     ->get();
        

        $gigs = Gig::with(['client', 'technician', 'machine'])
        ->whereDate('start_datetime', Carbon::today()) // Ensure it's today
        // ->whereBetween('start_datetime', [Carbon::now()->addHour(), Carbon::now()->addHour()->addMinutes(5)]) // Check if it's 1 hour away within a 5-min window
        // ->where('start_datetime', Carbon::now()->addHour())
        ->orderBy('start_datetime', 'ASC')
        ->get();

        foreach ($gigs as $gig) {
            $startTime = Carbon::parse($gig->start_datetime);
            $hoursPassed = $startTime->diffInHours(Carbon::now());


                
            $notification_response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post(url('/api/notify/store'), [
                'name' => 'Job Reminder',
                'content' => 'One Hour Until Gig #'.$gig->gig_cryptic.' ' . $gig->machine->machine_type. '. Are you ready" and "30 minutes until your next gig, get moving.',
                'user_id' => $gig->assigned_tech_id,
                'type' => 1, // 1 = GENERAL; 2=GUILD; 3=OTHER
                'icon_type' => 'fa-solid fa-briefcase',
            ]);

            // Get the response data
            $notif_json = $notification_response->json();
            $this->info("Gig #{$gig->gig_cryptic} started {$hoursPassed} hours ago.");
            Log::info("Gig #{$gig->gig_cryptic} started {$hoursPassed} hours ago.");
            \Log::info($notif_json);
            
            // Here you can trigger notification logic (SMS, Push, etc.)
        }

        $this->info('Custom cron job execution completed.');
    }
}
