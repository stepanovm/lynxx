<?php

namespace bin\Command\CreateDomainModelEntity;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CommandCreate extends Command
{
    protected function configure()
    {
        $this->setName('create:entity')
            ->setDescription('Create a new Entity with mapper and collection classes')
            ->addArgument('name', InputArgument::REQUIRED, 'name of Entity');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $responseInfo = "";
            $entityName = ucfirst($input->getArgument('name'));

            // create directories
            mkdir(__DIR__ . '/../../../app/model/Entity/' . $entityName . '/mapper/', 0777, true);

            // add files
            // 1. domain object
            $newClassBody = file_get_contents(__DIR__ . '/template_domain');
            $newClassPath = __DIR__ . '/../../../app/model/Entity/' . $entityName . '/' . $entityName . '.php';
            file_put_contents($newClassPath, str_replace('@@entityname@@', $entityName, $newClassBody));
            $responseInfo .= "\t" . $newClassPath;

            // 2. mapper
            $newClassBody = file_get_contents(__DIR__ . '/template_mapper');
            $newClassPath = __DIR__ . '/../../../app/model/Entity/' . $entityName . '/mapper/' . $entityName . 'Mapper.php';
            file_put_contents($newClassPath, str_replace('@@entityname@@', $entityName, $newClassBody));
            $responseInfo .= PHP_EOL . "\t" . $newClassPath;

            // 3. collection
            $newClassBody = file_get_contents(__DIR__ . '/template_collection');
            $newClassPath = __DIR__ . '/../../../app/model/Entity/' . $entityName . '/mapper/' . $entityName . 'Collection.php';
            file_put_contents($newClassPath, str_replace('@@entityname@@', $entityName, $newClassBody));
            $responseInfo .= PHP_EOL . "\t" . $newClassPath;

            // 4. deffered collection
            $newClassBody = file_get_contents(__DIR__ . '/template_deffered_collection');
            $newClassPath = __DIR__ . '/../../../app/model/Entity/' . $entityName . '/mapper/' . $entityName . 'DefferedCollection.php';
            file_put_contents($newClassPath, str_replace('@@entityname@@', $entityName, $newClassBody));
            $responseInfo .= PHP_EOL . "\t" . $newClassPath;

            // and finally add dependency to mappers map
            $mappersMapFileBody = file_get_contents(__DIR__ . '/../../../app/config/mappers_map.php');
            $dptxt = "\t\app\model\Entity\\".$entityName."\\".$entityName."::class => \app\model\Entity\\".$entityName."\mapper\\".$entityName."Mapper::class,\n];";
            file_put_contents(__DIR__ . '/../../../app/config/mappers_map.php', str_replace('];', $dptxt, $mappersMapFileBody));
            $responseInfo .= PHP_EOL . "\tmappers_map updated!";

            $output->writeln("\n<info>Entity created:</info>");
            $output->writeln($responseInfo);
            return Command::SUCCESS;

        } catch (\Throwable $ex) {
            $output->writeln("<error>Error!</error>");
            $output->writeln($ex->getMessage());
            return Command::FAILURE;
        }


    }
}