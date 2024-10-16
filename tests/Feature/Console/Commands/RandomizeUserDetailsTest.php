<?php

namespace Tests\Feature\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Tests\TestCase;
use Tests\Traits\RefreshTestingDatabase;

class RandomizeUserDetailsTest extends TestCase
{
    use RefreshTestingDatabase;

    /** @test */
    public function it_updates_user_details_with_valid_user_id(): void
    {
        $user = $this->makeUser()->create()->first();


        $this->artisan('update:user', ['user_id' => $user->id])
            ->assertExitCode(Command::SUCCESS);

        $updatedUser = User::find($user->id);
        $this->assertNotNull($updatedUser);
        $this->assertNotEquals($user->first_name, $updatedUser->first_name);
        $this->assertNotEquals($user->last_name, $updatedUser->last_name);
        $this->assertNotEquals($user->time_zone, $updatedUser->time_zone);
    }

    /** @test */
    public function it_returns_error_when_no_user_id_is_provided(): void
    {
        $this->artisan('update:user')
            ->assertExitCode(Command::FAILURE);
    }

    /** @test */
    public function it_returns_error_when_invalid_user_id_is_provided(): void
    {
        $this->artisan('update:user', ['user_id' => 999])
            ->assertExitCode(Command::FAILURE);
    }
}
