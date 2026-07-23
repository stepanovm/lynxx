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
            return null;
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
        try {
            $this->pdo->beginTransaction();
            $stmt = $this->pdo->prepare($migration->up());
            $stmt->execute();
            $stmt = $this->pdo->prepare("INSERT INTO migrations (name) VALUES ('" . preg_replace('#^.*\\\\#', '', get_class($migration))  . "')");
            $stmt->execute();
            $this->pdo->commit();
        } catch (\Throwable $ex) {
            $this->pdo->rollBack();
            throw $ex;
        }
    }


    public function downMigration(AbstractMigration $migration)
    {
        try {
            $this->pdo->beginTransaction();
            $stmt = $this->pdo->prepare($migration->down());
            $stmt->execute();
            $stmt = $this->pdo->prepare("DELETE FROM migrations WHERE name = '" . preg_replace('#^.*\\\\#', '', get_class($migration)) . "'");
            $stmt->execute();
            $this->pdo->commit();
        } catch (\Throwable $ex) {
            $this->pdo->rollBack();
            throw $ex;
        }
    }

    public function createMigrationsTable()
    {
        try {
            $this->pdo->beginTransaction();
            $stmt = $this->pdo->prepare("
                CREATE TABLE IF NOT EXISTS `migrations` (
                  `id` int NOT NULL,
                  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  `name` varchar(255) NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    
                ALTER TABLE `migrations` ADD PRIMARY KEY (`id`);
    
                ALTER TABLE `migrations` MODIFY `id` int NOT NULL AUTO_INCREMENT;
            ");

            $stmt->execute();
            $stmt->closeCursor();
            $this->pdo->commit();

        } catch (\Throwable $ex) {
            $this->pdo->rollBack();
            throw $ex;
        }
    }


}

