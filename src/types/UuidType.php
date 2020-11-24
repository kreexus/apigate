<?php

namespace mmaurice\apigate\types;

class UuidType extends \mmaurice\apigate\classes\Format
{
    public static function valide(&$field, $callback = null, $options = [])
    {
        parent::valide($field, $callback, $options);

        if (preg_match('/^([0-9a-f]{8}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{12})$/i', trim($field))) {
            return true;
        }

        return false;
    }
}
