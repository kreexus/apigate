<?php

namespace mmaurice\apigate;

use \mmaurice\apigate\classes\ClientConfig;

abstract class Client extends \mmaurice\apigate\classes\Client
{
    public function __construct()
    {
        self::$namespace = __NAMESPACE__;

        return parent::__construct();
    }
}
