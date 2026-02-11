<?php

namespace App\Console\Commands;

use App\Models\VehicleDocument;
use App\Models\User;
use App\Notifications\VehicleDocumentExpired;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;

class CheckVehicleDocuments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vehicles:check-documents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for vehicle documents expiring in 7 days or less and notify admins';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expiring documents...');

        // 1. Documents expiring exactly in 7 days (Warning)
        $targetDate = Carbon::now()->addDays(7)->toDateString();
        $warningDocs = VehicleDocument::with('vehicle')
            ->whereDate('expires_at', $targetDate)
            ->where('status', 'active') // Assuming soft deletes aren't enough or status column exists
            ->get();

        // 2. Documents expiring TODAY (Danger)
        $today = Carbon::now()->toDateString();
        $dangerDocs = VehicleDocument::with('vehicle')
            ->whereDate('expires_at', $today)
            ->where('status', 'active')
            ->get();

        $admins = User::whereIn('role', ['admin', 'supervisor'])->get();

        $count = 0;

        foreach ($warningDocs as $doc) {
            foreach ($admins as $admin) {
                // Check if already notified recently (last 2 days) to avoid spam
                $lastNotification = $admin->notifications()
                    ->where('type', VehicleDocumentExpired::class)
                    ->where('data->document_type', $doc->type)
                    ->where('data->vehicle_id', $doc->vehicle->id)
                    ->where('created_at', '>=', Carbon::now()->subDays(2))
                    ->first();

                if (!$lastNotification) {
                    $admin->notify(new VehicleDocumentExpired($doc, 7));
                    $this->info("Warning sent to {$admin->email} for vehicle: {$doc->vehicle->plate} - {$doc->type}");
                    $count++;
                }
            }
        }

        foreach ($dangerDocs as $doc) {
            foreach ($admins as $admin) {
                // Check if already notified recently (last 1 day for danger)
                $lastNotification = $admin->notifications()
                    ->where('type', VehicleDocumentExpired::class)
                    ->where('data->document_type', $doc->type)
                    ->where('data->vehicle_id', $doc->vehicle->id)
                    ->where('created_at', '>=', Carbon::now()->subDays(1))
                    ->first();

                if (!$lastNotification) {
                    $admin->notify(new VehicleDocumentExpired($doc, 0));
                    $this->error("Danger sent to {$admin->email} for vehicle: {$doc->vehicle->plate} - {$doc->type}");
                    $count++;
                }
            }
        }

        $this->info("Check complete. {$count} notifications sent.");
    }
}
