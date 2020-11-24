<?php

namespace mmaurice\apigate\schemas;

use \mmaurice\apigate\exceptions\SchemaException;
use \mmaurice\qurl\Response;

class DataSchema extends \mmaurice\apigate\builders\SchemaBuilder
{
    public static function build(Response $response, $asArray = false)
    {
        if (strpos($response->getResponseHeader()['content-type'], 'application/json') === false) {
            if (strlen($response->getRawResponseBody()) > 0) {
                throw new SchemaException($response->getRawResponseBody());
            }

            throw new SchemaException("Received data is not JSON.");
        }

        $arguments = json_decode($response->getRawResponseBody(), true);

        $class = get_called_class();

        return new $class($arguments);
    }

    protected function createFromArray(array $fields = [])
    {
        unset($this->rules);

        if (is_array($fields) and !empty($fields)) {
            foreach ($fields as $fieldName => $fieldValue) {
                $this->$fieldName = json_decode(json_encode($fieldValue));
            }
        }
    }
}
