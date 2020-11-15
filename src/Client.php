<?php

namespace mmaurice\apigate;

use \mmaurice\apigate\components\ClientConfigComponent;

class Client extends \mmaurice\apigate\components\ClientComponent
{
    public function __construct()
    {
        self::$namespace = __NAMESPACE__;

        return parent::__construct();
    }
}
