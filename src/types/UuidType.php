<?php

namespace mmaurice\apigate\types;

class UuidType extends \mmaurice\apigate\classes\Format
{
    protected static $options = [
        'mask' => '/^([0-9a-f]{8}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{12})$/i',
    ];

    public static function valide(&$field, $options = [])
    {
        parent::valide($field, $callback, $options);

        if (preg_match(static::$options, trim($field))) {
            return true;
        }

        return false;
    }
}
