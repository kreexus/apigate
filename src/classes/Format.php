<?php

namespace mmaurice\apigate\classes;

abstract class Format
{
    protected static $options = [];

    public static function valide(&$field, $callback = null, $options = [])
    {
        $options = array_merge(self::$options, $options);

        $field = self::format($field, $callback, $options);

        return true;
    }

    protected static function format($field, $callback = null, $options = [])
    {
        if (!is_null($callback)) {
            $field = $callback($field, $options);
        }

        return $field;
    }
}
