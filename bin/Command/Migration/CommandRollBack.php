<?php

namespace bin\Command\Migration;

use Lynxx\Migrations\AbstractMigration;
use Lynxx\Migrations\MigrationList;
use Lynxx\Migrations\MigrationRemoteManager;
use Lynxx\Migrations\MigrationsManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CommandRollBack extends Command
{
    private MigrationList $migrationList;
    private MigrationRemoteManager $migrationRemoteManager;
    private MigrationsManager $migrationsManager;

    /**
     * @param MigrationList $migrationList
     */
    public function __construct(MigrationList $migrationList, MigrationRemoteManager $migrationRemoteManager, MigrationsManager $migrationsManager)
    {
        parent::__construct();
        $this->migrationList = $migrationList;
        $this->migrationRemoteManager = $migrationRemoteManager;
        $this->migrationsManager = $migrationsManager;
    }


    protected function configure()
    {
        $this
            ->setName('migrate:rollback')
            ->setDescription('roll back last migration');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $migrations = $this->migrationList->get();

        $className = "app\migrations\\" . $migrations[array_key_last($migrations)];
        /** @var AbstractMigration $migration */
        $migration = new $className;

        $this->migrationsManager->rollBackMigrations($migration);


        return Command::SUCCESS;
    }
}