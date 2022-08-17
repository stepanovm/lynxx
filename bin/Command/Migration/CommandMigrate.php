<?php

namespace bin\Command\Migration;

use Lynxx\Migrations\AbstractMigration;
use Lynxx\Migrations\MigrationList;
use Lynxx\Migrations\MigrationsManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class CommandMigrate extends Command
{
    private MigrationsManager $migrationsManager;

    /**
     * @param MigrationList $migrationsManager
     */
    public function __construct(MigrationsManager $migrationsManager)
    {
        parent::__construct();
        $this->migrationsManager = $migrationsManager;
    }


    protected function configure()
    {
        $this->setName('migrate:run');
        $this->setDescription('run all migrations up');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var AbstractMigration[] $appliedMigrations */
        $appliedMigrations = $this->migrationsManager->applyNewMigrations();
        if(empty($appliedMigrations)){
            $output->writeln('Database is already up-to-date');
            return Command::SUCCESS;
        }

        foreach ($appliedMigrations as $migration) {
            $output->writeln($migration->getDescription());

            $migration->up();
        }

        return Command::SUCCESS;
    }
}