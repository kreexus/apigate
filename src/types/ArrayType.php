<?php

namespace mmaurice\apigate\types;

class ArrayType extends \mmaurice\apigate\classes\Format
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
