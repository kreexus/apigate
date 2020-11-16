<?php

namespace mmaurice\apigate\schemas;

use \mmaurice\apigate\exceptions\Exception;
use \mmaurice\qurl\Response;

class DataSchema extends \mmaurice\apigate\builders\SchemaBuilder
{
    protected static function createObject(Response $response, $asArray = false)
    {
        if (strpos($response->getHeader('Content-Type')[0], 'application/json') === false) {
            if (strlen($response->getBody()) > 0) {
                throw new Exception($response->getBody());
            }

            throw new Exception("Received data is not JSON.");
        }

        $arguments = json_decode($response->getBody(), true);

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
