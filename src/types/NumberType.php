<?php

namespace mmaurice\apigate\types;

class NumberType extends \mmaurice\apigate\classes\Format
{
    public function valide(&$field, $callback = null)
    {
        parent::valide($field, $callback);

        return true;
    }
}
