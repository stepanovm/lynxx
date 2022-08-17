<?php

namespace Lynxx\Migrations;

use PDO;

class MigrationRemoteManager
{
    private PDO $pdo;
    private ?string $lastRemoteMigrationName = null;


    /**
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     *
     * @return string|null
     */
    public function getLastRemoteMigrationName(): ?string
    {
        if (!is_null($this->lastRemoteMigrationName)) {
            return $this->lastRemoteMigrationName;
        }

        try {
            $stmt = $this->pdo->query('SELECT * FROM migrations ORDER BY name DESC LIMIT 1');
            $stmt->execute();
            $res = $stmt->fetch();

            if (!$res || !array_key_exists('name', $res)) {
                return null;
            }

            $this->lastRemoteMigrationName = $res['name'];
            return $res['name'];

        } catch (\Throwable $ex) {
            throw new \Exception($ex->getMessage());
        }


    }

    /**
     * @param string $name
     * @return void
     */
    public function setLastRemoteMigrationName(string $name): void
    {
        $this->lastRemoteMigrationName = $name;
    }

    public function upMigration(AbstractMigration $migration)
    {
        $this->pdo->beginTransaction();
        $stmt = $this->pdo->prepare($migration->up());
        $stmt->execute();
        $stmt = $this->pdo->prepare("INSERT INTO migrations (name) VALUES ('" . preg_replace('#^.*\\\\#', '', get_class($migration))  . "')");
        $stmt->execute();
        $this->pdo->commit();
    }


    public function downMigration(AbstractMigration $migration)
    {
        $this->pdo->beginTransaction();
        $stmt = $this->pdo->prepare($migration->down());
        $stmt->execute();
        $stmt = $this->pdo->prepare("DELETE FROM migrations WHERE name = '" . preg_replace('#^.*\\\\#', '', get_class($migration)) . "'");
        $stmt->execute();
        $this->pdo->commit();
    }


}

