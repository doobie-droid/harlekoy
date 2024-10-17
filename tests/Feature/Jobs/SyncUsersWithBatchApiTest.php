<?php

namespace Tests\Unit\Jobs;

use App\Jobs\SyncUsersWithBatchApi;
use App\Models\User;
use App\Services\BatchService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;
use Tests\Traits\RefreshTestingDatabase;

class SyncUsersWithBatchApiTest extends TestCase
{
    use RefreshTestingDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_should_not_sync_users_if_less_than_1000_unSynced_users_exist_and_less_than_50_minutes_have_passed()
    {
        Carbon::setTestNow(Carbon::createFromTime(10, 40));
        $this->makeUser(500)->create(['synced_with_batch_api' => false]);

        (new SyncUsersWithBatchApi())->handle();

        $this->assertDatabaseMissing('users', ['synced_with_batch_api' => true]);
    }

    /** @test */
    public function it_should_sync_users_if_more_than_1000_unSynced_users()
    {

        $this->makeUser(1500)->create(['synced_with_batch_api' => false]);

        (new SyncUsersWithBatchApi())->handle();

        $this->assertDatabaseHas('users', ['synced_with_batch_api' => true]);
        $this->assertDatabaseMissing('users', ['synced_with_batch_api' => false]);
    }


    /** @test */
    public function it_should_not_update_users_if_sync_fails()
    {
        $this->makeUser(1500)->create(['synced_with_batch_api' => false]);

        $this->mockBatchApi();

        (new SyncUsersWithBatchApi())->handle();

        $unSyncedUsers = User::where('synced_with_batch_api', false)->count();
        $syncedUsers = User::where('synced_with_batch_api', true)->count();

        $this->assertEquals(1000, $unSyncedUsers);
        $this->assertEquals(500, $syncedUsers);
    }

    /** @test */
    public function it_should_log_each_synced_user(): void
    {
        $this->makeUser(2000)->create(['synced_with_batch_api' => false]);


        Log::shouldReceive('info')
            ->withArgs(function ($message) {
                $this->assertStringContainsString('Subscriber', $message);
                return true;
            })
            ->times(2000);

        (new SyncUsersWithBatchApi())->handle();
    }

    /** @test */
    public function it_syncs_users_in_the_last_ten_minutes_when_count_is_not_up_to_1000(): void
    {
        $this->makeUser(200)->create(['synced_with_batch_api' => false]);

        Carbon::setTestNow(Carbon::createFromTime(10, 52));



        (new SyncUsersWithBatchApi())->handle();

        $this->assertDatabaseHas('users', ['synced_with_batch_api' => true]);
        $this->assertDatabaseMissing('users', ['synced_with_batch_api' => false]);
    }

    private function mockBatchApi(): void
    {
        $batchApiMock = Mockery::mock(BatchService::class);
        $batchApiMock->shouldReceive('updateUsersInBatch')
            ->twice()
            ->with(Mockery::type('Illuminate\Database\Eloquent\Collection'))
            ->andReturn(false, true);


        $this->app->instance(BatchService::class, $batchApiMock);
    }
}
