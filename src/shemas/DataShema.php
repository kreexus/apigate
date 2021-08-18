<?php

namespace mmaurice\apigate\shemas;

use \mmaurice\apigate\exceptions\ShemaException;
use \mmaurice\qurl\Response;

class DataShema extends \mmaurice\apigate\builders\ShemaBuilder
{
    public static function build(Response $response, $asArray = false)
    {
        if (strpos($response->getResponseHeader()['content-type'], 'application/json') === false) {
            if (strlen($response->getRawResponseBody()) > 0) {
                throw new ShemaException($response->getRawResponseBody());
            }

            throw new ShemaException("Received data is not JSON.");
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
