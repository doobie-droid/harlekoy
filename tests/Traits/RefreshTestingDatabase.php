<?php

namespace Tests\Traits;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Support\Carbon;

trait RefreshTestingDatabase
{
    use DatabaseTestTransactions;

    /**
     * The migrator instance.
     *
     * @var \Illuminate\Database\Migrations\Migrator
     */
    private $migrator;


    protected function refreshTestDatabase(): void
    {
        if (!RefreshDatabaseState::$migrated) {
            $this->runMigrationsIfNecessary();

            $this->app[Kernel::class]->setArtisan(null);

            RefreshDatabaseState::$migrated = true;
        }

        $this->beginDatabaseTransaction();
    }

    protected function runMigrationsIfNecessary(): void
    {
        $this->migrator = app('migrator');
        $files = $this->migrator->getMigrationFiles($this->migrator->paths());


        $pendingMigrations = array_diff(
            array_keys($files),
            $this->getRanMigrations()
        );

        if ($pendingMigrations || !$this->isMigratedFileExists()) {
            $this->artisan('migrate:fresh');
            $this->createMigratedFile();
        }
    }

    /**
     * Gets ran migrations with repository check
     *
     * @return array
     */
    public function getRanMigrations()
    {
        if (!$this->migrator->repositoryExists()) {
            return [];
        }

        return $this->migrator->getRepository()->getRan();
    }


    protected function createMigratedFile(): void
    {
        file_put_contents($this->migratedFilePath(), Carbon::now());
    }

    protected function migratedFilePath(): string
    {
        return base_path('.phpunit.test_database.migrated');
    }

    protected function isMigratedFileExists(): bool
    {
        return file_exists($this->migratedFilePath());
    }
}
