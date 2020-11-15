<?php

namespace mmaurice\apigate\formats;

class ArrayFormat extends \mmaurice\apigate\components\FormatComponent
{
    public static function valide(&$fields, $callback = null, $options = [])
    {
        if (is_array($fields) and !empty($fields)) {
            foreach ($fields as $key => $field) {
                parent::valide($fields[$key], $callback, $options);
            }
        }

        return true;
    }
}
