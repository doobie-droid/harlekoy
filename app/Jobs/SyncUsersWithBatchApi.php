<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\BatchService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncUsersWithBatchApi implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $batchApiService;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->batchApiService = app(BatchService::class);
    }

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

        $unSyncedUserIds = [];
        if ($unSyncedUsers >= 1000) {
            User::where('synced_with_batch_api', false)
                ->chunk(1000, function ($users) use (&$unSyncedUserIds) {
                    if ($this->batchApiService->updateUsersInBatch($users)) {
                        $unSyncedUserIds = array_merge($unSyncedUserIds, $users->pluck('id')->toArray());
                    }
                });
        }
        if (!empty($unSyncedUserIds)) {
            User::whereIn('id', $unSyncedUserIds)->update(['synced_with_batch_api' => true]);
        }
    }
}
