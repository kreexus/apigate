<?php

namespace mmaurice\apigate\schemas;

use \mmaurice\apigate\exceptions\Exception;
use \mmaurice\qurl\Response;

class ErrorSchema extends \mmaurice\apigate\schemas\DataSchema
{
    public static function build(Response $response, $asArray = false)
    {
        if (!in_array(intdiv(intval($response->getResponseCode()), 100), [4, 5])) {
            throw new Exception("Received wrong code: \"{$response->getResponseCode()}\".");
        }

        return self::createObject($response, $asArray);
    }
}
