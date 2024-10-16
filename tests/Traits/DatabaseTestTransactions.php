<?php

namespace Tests\Traits;

use Illuminate\Support\Facades\DB;

trait DatabaseTestTransactions
{
    /**
     * Handle database transactions on the specified connections.
     *
     * @return void
     */
    public function beginDatabaseTransaction()
    {
        DB::beginTransaction();

        $this->beforeApplicationDestroyed(function () {
            foreach ($this->connectionsToTransact() as $name) {
                DB::rollBack();
            }
        });
    }

    /**
     * The database connections that should have transactions.
     *
     * @return array
     */
    protected function connectionsToTransact()
    {
        return property_exists($this, 'connectionsToTransact')
            ? $this->connectionsToTransact : [null];
    }
}
