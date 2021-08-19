<?php

namespace mmaurice\apigate\builders;

use \mmaurice\apigate\Client;
use \mmaurice\apigate\exceptions\ShemaException;
use \mmaurice\apigate\shemas\ErrorShema;
use \mmaurice\qurl\Response;

abstract class ShemaBuilder extends \mmaurice\apigate\classes\Shema
{
    static public $response;

    public function __construct(array $arguments = [])
    {
        $this->createFromArray($arguments);
    }

    public static function build(Response $response, $asArray = false)
    {
        $responseHeaders = $response->getResponseHeader();
        $responseRawBody = $response->getRawResponseBody();

        if (array_key_exists('content-type', $responseHeaders) and (strpos($responseHeaders['content-type'], 'application/json') === false)) {
            if (strlen($responseRawBody) > 0) {
                throw new ShemaException($responseRawBody);
            }

            throw new ShemaException("Received data is not JSON.");
        }

        $arguments = json_decode($responseRawBody, true);

        $class = get_called_class();

        if ($asArray) {
            return array_map(function ($class, $field) {
                return new $class($field);
            }, array_fill(0, count($arguments), $class), $arguments);
        }

        return new $class($arguments);
    }
}
