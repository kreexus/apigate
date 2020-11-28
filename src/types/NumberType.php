<?php

namespace mmaurice\apigate\types;

class NumberType extends \mmaurice\apigate\classes\Format
{
    public static function valide(&$field, $options = [])
    {
        return true;
    }
}
