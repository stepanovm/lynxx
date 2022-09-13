<?php

namespace Lynxx\Migrations;


abstract class AbstractMigration
{
    abstract public function up(): string;
    abstract public function down(): string;
    abstract public function getDescription(): ?string;
}