<?php

namespace mmaurice\apigate\configs;

abstract class Config extends \mmaurice\apigate\classes\Storage
{
    public function __construct($options = [])
    {
        $this->setOptions($options);
    }
}
