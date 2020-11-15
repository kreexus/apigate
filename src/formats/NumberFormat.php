<?php

namespace mmaurice\apigate\formats;

class NumberFormat extends \mmaurice\apigate\components\FormatComponent
{
    public static function valide(&$field, $callback = null, $options = [])
    {
        parent::valide($field, $callback, $options);

        return true;
    }
}
