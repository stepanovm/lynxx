<?php

namespace bin\Command\CreateDomainModelEntity;

use Lynxx\Lynxx;
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

            if (stripos($entityName, '\\') !== false) {
                $entityPath = explode('\\', $input->getArgument('name'));
            } else {
                $entityPath = explode('/', $input->getArgument('name'));
            }

            // last elem is name of entity
            $entityName = ucfirst(array_pop($entityPath));

            // other array's items
            if (!empty($entityPath)) {
                foreach ($entityPath as &$path) {
                    $path = ucfirst($path);
                }
            }

            // prepare directory name and namespace name
            $pathDir = __DIR__ . '/../../../app/model/Entity/' . (empty($entityPath) ? '' : implode('/', $entityPath)) . '/' . $entityName;
            $pathNamespace = empty($entityPath) ? '' : implode('\\', $entityPath) . '\\';

            // create directories
            mkdir($pathDir . '/mapper/', 0777, true);

            // add files
            // 1. domain object
            $newClassBody = file_get_contents(__DIR__ . '/template_domain');
            $newClassPath = $pathDir . '/' . $entityName . '.php';
            file_put_contents($newClassPath, str_replace(['@@entityname@@', '$$entityPath$$'], [$entityName, $pathNamespace], $newClassBody));
            $responseInfo .= "\t" . $newClassPath;

            // 2. mapper
            $newClassBody = file_get_contents(__DIR__ . '/template_mapper');
            $newClassPath = $pathDir . '/mapper/' . $entityName . 'Mapper.php';
            file_put_contents($newClassPath, str_replace(['@@entityname@@', '$$entityPath$$'], [$entityName, $pathNamespace], $newClassBody));
            $responseInfo .= PHP_EOL . "\t" . $newClassPath;

            // 3. collection
            $newClassBody = file_get_contents(__DIR__ . '/template_collection');
            $newClassPath = $pathDir . '/mapper/' . $entityName . 'Collection.php';
            file_put_contents($newClassPath, str_replace(['@@entityname@@', '$$entityPath$$'], [$entityName, $pathNamespace], $newClassBody));
            $responseInfo .= PHP_EOL . "\t" . $newClassPath;

            // 4. deffered collection
            $newClassBody = file_get_contents(__DIR__ . '/template_deffered_collection');
            $newClassPath = $pathDir . '/mapper/' . $entityName . 'DefferedCollection.php';
            file_put_contents($newClassPath, str_replace(['@@entityname@@', '$$entityPath$$'], [$entityName, $pathNamespace], $newClassBody));
            $responseInfo .= PHP_EOL . "\t" . $newClassPath;

            // and finally add dependency to mappers map
            $mappersMapFileBody = file_get_contents(__DIR__ . '/../../../app/config/mappers_map.php');
            $dptxt = "\t\app\model\Entity\\".$pathNamespace.$entityName."\\".$entityName."::class => \app\model\Entity\\".$pathNamespace.$entityName."\mapper\\".$entityName."Mapper::class,\n];";
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