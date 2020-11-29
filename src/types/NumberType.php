<?php

namespace mmaurice\apigate\types;

class NumberType extends \mmaurice\apigate\classes\Format
{
    public static function valide(&$field, $callback = null, $options = [])
    {
        parent::valide($field, $callback, $options);

        return true;
    }
}
