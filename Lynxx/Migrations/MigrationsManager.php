<?php

namespace Lynxx\Migrations;

class MigrationsManager
{
    private MigrationList $migrationList;
    private MigrationRemoteManager $migrationRemoteManager;

    /**
     * @param MigrationList $migrationList
     * @param MigrationRemoteManager $migrationRemoteManager
     */
    public function __construct(MigrationList $migrationList, MigrationRemoteManager $migrationRemoteManager)
    {
        $this->migrationList = $migrationList;
        $this->migrationRemoteManager = $migrationRemoteManager;
    }

    /**
     *
     *
     * @return int
     * @throws \Exception
     */
    public function getCurrentMigrationIndex(): ?int
    {
        if($lastRemoteMigration = $this->migrationRemoteManager->getLastRemoteMigrationName()) {
            // If remote migration exists

            $migrations = $this->migrationList->get();
            for ($i = (count($migrations) - 1); $i >= 0; $i--) {
                if($migrations[$i] === $lastRemoteMigration) {
                    return $i;
                }
            }
            throw new \Exception('Remote migration name not found in MigrationList!');
        } else {
            return null;
        }
    }


    /**
     * @return AbstractMigration[]|null
     * @throws \Exception
     */
    public function getMigrationsToApply(): ?array
    {
        $currentMigrationIndex = $this->getCurrentMigrationIndex();
        $lastMigrationIndex = count($this->migrationList->get()) - 1;
        $migrationsToApply = [];

        // Если индексы совпадают - последняя миграция уже была применена, ничего делать не нужно.
        if($currentMigrationIndex === $lastMigrationIndex) {
            return null;
        }


        if(is_null($currentMigrationIndex)) {
            // Если миграций еще не было, придет пустой (null) индекс из БД. Вручную поставим ему 0 - первый индекс, чтобы в цикле использовать.
            $this->migrationRemoteManager->createMigrationsTable();
            $currentMigrationIndex = 0;
        } else {
            // В остальных, обычных случаях мы текущий индекс сдвигаем вперед на первую не примененную миграцию
            $currentMigrationIndex++;
        }

        while ($currentMigrationIndex <= $lastMigrationIndex) {
            $className = "app\migrations\\" . $this->migrationList->get()[$currentMigrationIndex];
            $migration = new $className;
            $migrationsToApply[] = $migration;
            $currentMigrationIndex++;
        }

        return $migrationsToApply ?? null;
    }



    /** Push all new migrations */
    public function applyNewMigrations(): array
    {
        $currentMigrationIndex = $this->getCurrentMigrationIndex();
        $lastMigrationIndex = count($this->migrationList->get()) - 1;
        $appliedMigrations = [];

        // Если индексы совпадают - последняя миграция уже была применена, ничего делать не нужно.
        if($currentMigrationIndex === $lastMigrationIndex) {
            return [];
        }

        // Если миграций еще не было, придет пустой (null) индекс из БД. Вручную поставим ему 0 - первый индекс, чтобы в цикле использовать.
        if(is_null($currentMigrationIndex)) {
            $currentMigrationIndex = 0;
        }

        while ($currentMigrationIndex <= $lastMigrationIndex) {
            $className = "app\migrations\\" . $this->migrationList->get()[$currentMigrationIndex];
            $migration = new $className;
            $this->migrationRemoteManager->upMigration($migration);
            $appliedMigrations[] = $migration;
            $currentMigrationIndex++;
        }

        return $appliedMigrations;

    }

    public function rollBackMigrations(AbstractMigration $migration)
    {
        $this->migrationRemoteManager->downMigration($migration);

        $className = preg_replace('#^.*\\\\#', '', get_class($migration));
        $this->migrationList->remove($className);

        unlink(__DIR__ . '/../../app/migrations/'.$className.'.php');
    }

}