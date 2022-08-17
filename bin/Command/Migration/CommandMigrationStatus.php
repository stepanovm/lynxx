<?php

namespace bin\Command\Migration;

use Lynxx\Migrations\MigrationList;
use Lynxx\Migrations\MigrationRemoteManager;
use Lynxx\Migrations\MigrationsManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CommandMigrationStatus extends Command
{
    private MigrationList $migrationList;
    private MigrationsManager $migrationsManager;

    /**
     * @param MigrationList $migrationList
     * @param MigrationsManager $migrationsManager
     */
    public function __construct(MigrationList $migrationList, MigrationsManager $migrationsManager)
    {
        parent::__construct();
        $this->migrationList = $migrationList;
        $this->migrationsManager = $migrationsManager;
    }


    protected function configure()
    {
        $this->setName('migrate:status');
        $this->setDescription('getting full current mogrations info');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $currentMigrationIndex = $this->migrationsManager->getCurrentMigrationIndex();
        $list = $this->migrationList->get();

        $output->writeln('db version ::: ' . $list[$currentMigrationIndex] ?? 'NULL');
        $output->writeln('last unsent migration ::: ' . $list[count($list) - 1]);
        $output->writeln('unsent migrations count ::: ' . (count($list) - ($currentMigrationIndex ? ++$currentMigrationIndex : 0)));


        return Command::SUCCESS;
    }
}