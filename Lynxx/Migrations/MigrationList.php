<?php

namespace Lynxx\Migrations;

class MigrationList
{
    private array $list;
    private string $pathToListFile;

    public function __construct()
    {
        $this->pathToListFile = __DIR__ . '/../../app/migrations/list';
        if(!file_exists($this->pathToListFile)) {
            $this->list = [];
            return;
        }

        $list = unserialize(file_get_contents($this->pathToListFile));

        $this->list = is_array($list) ? $list : [];
    }


    public function add(string $migrationName)
    {
        $this->list[] = $migrationName;
        file_put_contents($this->pathToListFile, serialize($this->list));
    }

    /**
     * @param string $migrationName
     * @return void
     */
    public function remove(string $migrationName)
    {
        for($i = count($this->list) - 1; $i > 0; $i--) {
            if($this->list[$i] === $migrationName) {
                unset($this->list[$i]);
                return;
            }
        }
    }

    public function get()
    {
        return $this->list;
    }

    /**
     * @param string $mgName
     * @return bool
     */
    public function has(string $mgName): bool
    {
        return in_array($mgName, $this->list);
    }


}