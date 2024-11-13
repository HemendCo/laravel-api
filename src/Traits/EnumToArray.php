<?php

namespace Hemend\Api\Traits;

trait EnumToArray
{
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function values(): array
    {
        $values = array_column(self::cases(), 'value');
        return empty($values) ? self::names() : $values;
    }

    public static function valuesList(string $separator = ','): string
    {
        return implode($separator, self::values());
    }

    public static function array(): array
    {
        return array_combine(self::values(), self::names());
    }
}
