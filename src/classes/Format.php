<?php

namespace mmaurice\apigate\classes;

abstract class Format
{
    protected $options = [];

    public function __construct(array $options = [])
    {
        $this->options = array_merge($this->options, $options);
    }

    public function valide(&$field, $callback = null)
    {
        if (!is_null($callback)) {
            $field = $callback($field, $this->options);
        }

        return true;
    }
}
