<?php

namespace mmaurice\apigate\builders;

use \mmaurice\apigate\Client;
use \mmaurice\apigate\exceptions\Exception;
use \mmaurice\apigate\schemas\ErrorSchema;
use \mmaurice\qurl\Response;

abstract class SchemaBuilder extends \mmaurice\apigate\components\SchemaComponent
{
    static public $response;

    public function __construct(array $arguments = [])
    {
        $this->createFromArray($arguments);
    }

    public static function build(Response $response, $asArray = false)
    {
        if (strpos($response->getResponseHeader()['content-type'], 'application/json') === false) {
            if (strlen($response->getRawResponseBody()) > 0) {
                throw new Exception($response->getRawResponseBody());
            }

            throw new Exception("Received data is not JSON.");
        }

        $arguments = json_decode($response->getRawResponseBody(), true);

        $class = get_called_class();

        if ($asArray) {
            return array_map(function ($class, $field) {
                return new $class($field);
            }, array_fill(0, count($arguments), $class), $arguments);
        }

        return new $class($arguments);
    }
}
