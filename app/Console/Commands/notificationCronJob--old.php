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
    protected $description = 'Run notifications for both SMS & Push.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info('Notification Cron Job executed at ' . now());

        // Retrieve gigs scheduled for today
        $gigs = Gig::with(['client', 'technician', 'machine'])
            ->whereDate('start_datetime', Carbon::today())
            ->orderBy('start_datetime', 'ASC')
            ->get();

        foreach ($gigs as $gig) {
            $startTime = Carbon::parse($gig->start_datetime);

            // Only proceed if the gig is still in the future
            if (Carbon::now()->lt($startTime)) {
                $minutesUntilStart = Carbon::now()->diffInMinutes($startTime);

                // One Hour Notification (60 minutes before start)
                if ($minutesUntilStart === 60) {
                    $content = "One Hour Until " . ($gig->client->name ?? 'Client') . " " . ($gig->machine->machine_type ?? 'Machine') . ". Are you ready?";
                    \Log::info($content);
                    $this->sendNotification($gig->assigned_tech_id, $content, 1, 'fa-solid fa-briefcase');
                }

                // 30 Minutes Notification (30 minutes before start)
                if ($minutesUntilStart === 30) {
                    $content = "30 minutes until your next gig, get moving!";
                    \Log::info($content);
                    $this->sendNotification($gig->assigned_tech_id, $content, 1, 'fa-solid fa-briefcase');
                }
            }

            // New Job Notification: if the gig was created within the last 5 minutes
            if (Carbon::now()->diffInMinutes($gig->created_at) <= 5) {
                $content = "New Job added to schedule, check it out!";
                \Log::info($content);
                $this->sendNotification($gig->assigned_tech_id, $content, 1, 'fa-solid fa-briefcase');
            }
        }

        $this->info('Notification Cron Job execution completed.');
    }

    /**
     * Send notification via the API endpoint.
     *
     * @param int    $userId
     * @param string $content
     * @param int    $type     Notification type (e.g., 1 = GENERAL)
     * @param string $iconType
     */
    private function sendNotification($userId, $content, $type, $iconType)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post(url('/api/notify/store'), [
                'name'      => 'Job Reminder',
                'content'   => $content,
                'user_id'   => $userId,
                'type'      => $type, // 1 = GENERAL; 2 = GUILD; 3 = OTHER
                'icon_type' => $iconType,
            ]);

            $responseData = $response->json();
            Log::info("Notification sent: " . json_encode($responseData));
        } catch (\Exception $e) {
            Log::error("Error sending notification: " . $e->getMessage());
        }
    }
}
