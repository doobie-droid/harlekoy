<?php

namespace Tests\Unit\Observers;

use App\Jobs\SyncUsersWithBatchApi;
use App\Models\User;
use App\Services\BatchService;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;
use Tests\Traits\RefreshTestingDatabase;

class UserObserverTest extends TestCase
{
    use RefreshTestingDatabase;

    /** @test */
    public function it_does_not_update_synced_with_batch_api_field_when_non_describing_user_attributes_are_updated()
    {
        $user = $this->makeUser()->create(["synced_with_batch_api" => true])->first();

        $user->update(["password" => "new_password"]);
        $user->update(["remember_token" => "new_remember_token"]);
        $user->update(["synced_with_batch_api" => true]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'synced_with_batch_api' => true
        ]);
    }

    /** @test */
    public function it_updates_synced_with_batch_api_field_when_describing_user_attributes_are_updated()
    {
        $user = $this->makeUser()->create(["synced_with_batch_api" => true])->first();

        $user->update(["first_name" => "new_first_name"]);
        $user->update(["last_name" => "new_last_name"]);
        $user->update(["time_zone" => "new_time_zone"]);


        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'synced_with_batch_api' => false,
            'time_zone' => $user->time_zone
        ]);
    }
}
