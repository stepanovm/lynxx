<?php

namespace app\config\dbTypes;

class dateTime implements \app\model\core\dbTypeInterface
{
    /**
     * @param $value
     * @return \DateTimeImmutable
     * @throws \Exception
     */
    public function toPhpValue($value)
    {
        return new \DateTimeImmutable($value);
    }


    /**
     * @param $value
     * @return string
     */
    public function toDataBaseValue($value)
    {
        if ($value instanceof \DateTimeImmutable) {
            return $value->format('Y-m-d h-i-s');
        }
        throw new \DomainException('cannot convert, value is not instance of \DateTimeImmutable');
    }
}