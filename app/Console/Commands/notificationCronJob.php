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

        // Get all gigs happening today
        $gigs = Gig::with(['client', 'technician', 'machine'])
            ->whereDate('start_datetime', Carbon::today())
            ->orderBy('start_datetime', 'ASC')
            ->get();

        foreach ($gigs as $gig) {
            $startTime = Carbon::parse($gig->start_datetime);

            // Only process gigs that haven't started yet
            // if (!$startTime->isFuture()) {
            //     \Log::info('Only process gigs that haven\'t started yet');
            //     $this->info('Only process gigs that haven\'t started yet');
            //     continue;
            // }

            // Calculate minutes until the gig starts
            $minutesUntilGig = Carbon::now()->diffInMinutes($startTime);

            // If the gig starts in 1 hour or less
            if ($minutesUntilGig <= 60) {
                $cacheKeyOneHour = 'gig:' . $gig->gig_id . ':notified_one_hour';
                if (!cache()->has($cacheKeyOneHour)) {
                    $notification_response = Http::withHeaders([
                        'Content-Type' => 'application/json',
                    ])->post(url('/api/notify/store'), [
                        'name'    => 'Job Reminder',
                        'content' => 'One Hour Until Gig #' . $gig->gig_cryptic . ' ' . $gig->machine->machine_type . '. Are you ready?',
                        'user_id' => $gig->assigned_tech_id,
                        'type'    => 1, // 1 = GENERAL; 2 = GUILD; 3 = OTHER
                        'icon_type' => 'fa-solid fa-briefcase',
                    ]);

                    $notif_json = $notification_response->json();
                    $this->info("Sent 1-hour notification for Gig #{$gig->gig_cryptic}");
                    Log::info("Gig #{$gig->gig_cryptic} will start in {$minutesUntilGig} minutes. Sent 1-hour notification.");
                    Log::info($notif_json);

                    // Store cache key so that this notification is sent only once.
                    $ttl = max(60, Carbon::now()->diffInSeconds($startTime));
                    cache()->put($cacheKeyOneHour, true, $ttl);
                }
            }

            // If the gig starts in 30 minutes or less
            if ($minutesUntilGig <= 30) {
                $cacheKeyThirtyMin = 'gig:' . $gig->gig_id . ':notified_thirty_min';
                if (!cache()->has($cacheKeyThirtyMin)) {
                    $notification_response = Http::withHeaders([
                        'Content-Type' => 'application/json',
                    ])->post(url('/api/notify/store'), [
                        'name'    => 'Job Reminder',
                        'content' => '30 Minutes Until Gig #' . $gig->gig_cryptic . ' ' . $gig->machine->machine_type . '. Get moving!',
                        'user_id' => $gig->assigned_tech_id,
                        'type'    => 1, // 1 = GENERAL; 2 = GUILD; 3 = OTHER
                        'icon_type' => 'fa-solid fa-briefcase',
                    ]);

                    $notif_json = $notification_response->json();
                    $this->info("Sent 30-minute notification for Gig #{$gig->gig_cryptic}");
                    Log::info("Gig #{$gig->gig_cryptic} will start in {$minutesUntilGig} minutes. Sent 30-minute notification.");
                    Log::info($notif_json);

                    // Store cache key so that this notification is sent only once.
                    $ttl = max(60, Carbon::now()->diffInSeconds($startTime));
                    cache()->put($cacheKeyThirtyMin, true, $ttl);
                }
            }
        }

        $this->info('Custom cron job execution completed.');
    }
}
