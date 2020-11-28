<?php

namespace mmaurice\apigate\configs;

class Config extends \mmaurice\apigate\classes\Storage
{
    public function __construct($options = [])
    {
        $this->setOptions($options);
    }
}
