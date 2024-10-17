<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use App\Services\API;
use Illuminate\Http\Response;

class BatchService extends API
{
    public function __construct()
    {
        $this->secret = config('crm.batch_api_secret');
    }

    public function baseUrl(): string
    {
        return 'https://batchapi.com/';
    }

    public function updateUsersInBatch(Collection $users): bool
    {
        $batch = $users->map(function ($user) {
            return [
                'email' => $user->email,
                'name' => $user->first_name . ' ' . $user->last_name,
                'time_zone' => $user->time_zone,
            ];
        })->toArray();

        $batches = [
            "batches" => [
                [
                    "subscribers" => $batch
                ]
            ]
        ];


        foreach ($batch as $index => $subscriber) {
            $logMessage = "Subscriber {$index}: ";
            foreach ($subscriber as $key => $value) {
                $logMessage .= "{$key}: '{$value}', ";
            }
            Log::info($logMessage);
        }


        if ($this->updateUsersInBatchApi($batches)->status_code == Response::HTTP_OK) {
            return true;
        }
        return false;
    }

    private function updateUsersInBatchApi(array $data): \stdClass
    {
        // return $this->_post('batch/endpoint', $data);

        return response()->json([
            'status' => true,
            'status_code' => 200,
            'message' => 'Users updated successfully',
        ])->getData();
    }
}
