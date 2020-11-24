<?php

namespace mmaurice\apigate\types;

class EnumType extends \mmaurice\apigate\classes\Format
{
    public static function valide(&$field, $callback = null, $options = [])
    {
        parent::valide($field, $callback, $options);

        if (!array_key_exists('enum', $options) or !is_array($options['enum']) or empty($options['enum'])) {
            return false;
        }

        if (!in_array($field, $options['enum'])) {
            return false;
        }

        return true;
    }
}
