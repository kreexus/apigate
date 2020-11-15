<?php

namespace mmaurice\apigate\components;

class ClientConfigComponent extends \mmaurice\apigate\components\StorageComponent
{
    public function __construct($options = [])
    {
        $this->setOptions($options);
    }
}
