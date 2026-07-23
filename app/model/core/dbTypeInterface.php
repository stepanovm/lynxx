<?php

namespace app\model\core;

interface dbTypeInterface
{
    public function toPhpValue($value);
    public function toDataBaseValue($value);
}