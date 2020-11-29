<?php

namespace mmaurice\apigate\types;

class EnumType extends \mmaurice\apigate\classes\Format
{
    public function valide(&$field, $callback = null)
    {
        parent::valide($field, $callback);

        if (!array_key_exists('enum', $this->options) or !is_array($this->options['enum']) or empty($this->options['enum'])) {
            return false;
        }

        if (!in_array($field, $this->options['enum'])) {
            return false;
        }

        return true;
    }
}
