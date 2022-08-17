<?php

namespace Lynxx\Migrations;

use Lynxx\DB;

class MigrationsFactory
{
    private MigrationList $migrationList;

    /**
     * @param MigrationList $migrationList
     */
    public function __construct(MigrationList $migrationList)
    {
        $this->migrationList = $migrationList;
    }


    /**
     * @return string|null
     */
    public function create(): ?string
    {
        $migrationName = "migration_" . date('YmdHis');
        $this->migrationList->add($migrationName);

        $newClassBody = file_get_contents(__DIR__ . '/../../Lynxx/Migrations/migration_template');
        file_put_contents(__DIR__ . '/../../app/migrations/' . $migrationName . '.php',
            str_replace('_CLASSNAME_', $migrationName, $newClassBody));

        return $migrationName ?? null;
    }




}