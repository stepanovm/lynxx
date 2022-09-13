<?php

namespace bin\Command\Migration;

use Lynxx\Migrations\AbstractMigration;
use Lynxx\Migrations\MigrationList;
use Lynxx\Migrations\MigrationRemoteManager;
use Lynxx\Migrations\MigrationsManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class CommandMigrate extends Command
{
    private MigrationsManager $migrationsManager;
    private MigrationRemoteManager $migrationRemoteManager;

    /**
     * @param MigrationList $migrationsManager
     */
    public function __construct(MigrationsManager $migrationsManager, MigrationRemoteManager $migrationRemoteManager)
    {
        parent::__construct();
        $this->migrationsManager = $migrationsManager;
        $this->migrationRemoteManager = $migrationRemoteManager;
    }


    protected function configure()
    {
        $this->setName('migrate:run');
        $this->setDescription('run all migrations up');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var AbstractMigration[] $migrationsToApply */
        $migrationsToApply = $this->migrationsManager->getMigrationsToApply();

        if (is_null($migrationsToApply)) {
            $output->writeln('<info>Database is already up-to-date</info>');
            return Command::SUCCESS;
        }

        $output->writeln("<info>Begin to apply migrations: </info>");
        foreach ($migrationsToApply as $migration) {
            $output->write($migration->getDescription() ?? 'applying ' . get_class($migration));
            $this->migrationRemoteManager->upMigration($migration);
            $output->writeln('... complete!');
        }

        return Command::SUCCESS;
    }
}