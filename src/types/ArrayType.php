<?php

namespace mmaurice\apigate\types;

class ArrayType extends \mmaurice\apigate\classes\Format
{
    public static function valide(&$fields, $callback = null, $options = [])
    {
        if (!is_array($fields)) {
            return false;
        }

        return true;
    }
}
