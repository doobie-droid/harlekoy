<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class RandomizeUserDetails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:user {user_id=-1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This updates the user\'s first_name, last_name and time_zone details to random values';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $userId = $this->argument('user_id');

        if ($userId == -1) {
            $this->error('Please provide a user id.');
            return Command::FAILURE;
        }

        $user = User::find($userId);

        if (!$user) {
            $this->error('Please provide a valid user id.');
            return Command::FAILURE;
        }

        //TODO: Change this to exhaustive enum list or faker extension pending more information on acceptable timezone type
        $randomTimezone = fake()->randomElement(["CET", "CST", "GMT+1"]);

        $user->first_name = fake()->firstName();
        $user->last_name = fake()->lastName();
        $user->time_zone = $randomTimezone;
        $user->save();

        $this->info(sprintf(
            "User details updated successfully.\nNew First Name: %s\nNew Last Name: %s\nNew Timezone: %s",
            $user->first_name,
            $user->last_name,
            $user->time_zone
        ));
        

        return Command::SUCCESS;
    }
}
