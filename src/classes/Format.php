<?php

namespace mmaurice\apigate\classes;

abstract class Format
{
    public static function valide(&$field, $options = [])
    {
        return true;
    }
}
