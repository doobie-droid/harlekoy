<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncUsersWithBatchApi implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $unSyncedUsers = User::where('synced_with_batch_api', false)
            ->limit(1000)
            ->count();


        if ($unSyncedUsers >= 1000) {

            User::where('synced_with_batch_api', false)
                ->chunk(1000, function ($users) {
                    $this->syncWithBatchApi($users);

                    User::whereIn('id', $users->pluck('id'))
                        ->update(['synced_with_batch_api' => true]);
                });
        }
    }

    /**
     * Format users and sync with batch API.
     *
     * @param \Illuminate\Support\Collection $users
     * @return void
     */
    protected function syncWithBatchApi($users): void
    {
        $batch = $users->map(function ($user) {
            return [
                'email' => $user->email,
                'name' => $user->first_name . ' ' . $user->last_name,
                'time_zone' => $user->time_zone,
            ];
        });

        $batches = [
            "batches" => [
                [
                    "subscribers" => $batch->toArray()
                ]
            ]
        ];
        $this->makeApiCall($batches);
    }

    protected function makeApiCall($batches)
    {
        $subscribers = $batches['batches'][0]['subscribers'];
        foreach($subscribers as $index => $subscriber) {
            $logMessage = "Subscriber {$index }: ";
            foreach ($subscriber as $key => $value) {
                $logMessage .= "{$key}: '{$value}', ";
            }
            Log::info($logMessage);
        }
    }
}
