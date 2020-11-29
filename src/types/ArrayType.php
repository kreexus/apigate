<?php

namespace mmaurice\apigate\types;

class ArrayType extends \mmaurice\apigate\classes\Format
{
    public function valide(&$fields, $callback = null)
    {
        if (is_array($fields) and !empty($fields)) {
            foreach ($fields as $key => $field) {
                parent::valide($fields[$key], $callback);
            }
        }

        return true;
    }
}
