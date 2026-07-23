<?php

namespace bin\Command\Migration;

use Lynxx\Migrations\MigrationsFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

class CommandCreateMigration extends Command
{
    private MigrationsFactory $migrationsFactory;

    /**
     * @param MigrationsFactory $migrationsFactory
     */
    public function __construct(MigrationsFactory $migrationsFactory)
    {
        parent::__construct();
        $this->migrationsFactory = $migrationsFactory;
    }


    protected function configure()
    {
        $this->setName('migrate:create');
        $this->setDescription('creating a new migration');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        if($migrationName = $this->migrationsFactory->create()) {
            $output->writeln("Миграция успешно создана: файл " . $migrationName);
            return Command::SUCCESS;
        }

        $output->writeln("Ошибка! Миграция не была создана", $output::VERBOSITY_VERY_VERBOSE);
        return Command::SUCCESS;
    }
}