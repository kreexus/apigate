<?php

namespace mmaurice\apigate\types;

class MaskedType extends \mmaurice\apigate\classes\Format
{
    public function valide(&$field, $callback = null)
    {
        parent::valide($field, $callback);

        if (array_key_exists('mask', $this->options) and !empty($this->options['mask'])) {
            if (preg_match($this->options['mask'], trim($field))) {
                return true;
            }
        }

        return false;
    }
}
