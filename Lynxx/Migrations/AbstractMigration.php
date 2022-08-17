<?php

namespace Lynxx\Migrations;


abstract class AbstractMigration
{
    abstract public function up();
    abstract public function down();
    abstract public function getDescription();
}