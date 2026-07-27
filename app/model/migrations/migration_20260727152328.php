<?php

namespace app\model\migrations;

use Lynxx\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class migration_20260727152328 extends AbstractMigration
{

    public function up(): string
    {
        // this up() migration is auto-generated, please modify it to your needs
        return "
            CREATE TABLE `user` (`id` INT NOT NULL AUTO_INCREMENT , `login` VARCHAR(50) NOT NULL , `password` VARCHAR(255) NOT NULL , `reg_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `email` VARCHAR(255) NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
            CREATE TABLE `user_session` (`id` INT NOT NULL AUTO_INCREMENT , `user_id` INT NOT NULL , `session` VARCHAR(255) NOT NULL , `date_create` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `userAgent` VARCHAR(255) NULL , `userIP` VARCHAR(16) NOT NULL , PRIMARY KEY (`id`), INDEX `user` (`user_id`)) ENGINE = InnoDB;
            ALTER TABLE `user_session` ADD FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
        ";
    }

    public function down(): string
    {
        // this down() migration is auto-generated, please modify it to your needs
        return "

        ";
    }


    public function getDescription() : string
    {
        return '';
    }
}