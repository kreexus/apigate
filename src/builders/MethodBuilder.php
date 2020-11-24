<?php

namespace mmaurice\apigate\builders;

use \mmaurice\apigate\Client;
use \mmaurice\apigate\schemas\DataSchema;
use \mmaurice\qurl\Request;
use \mmaurice\qurl\Response;

class MethodBuilder extends \mmaurice\apigate\components\SchemaComponent
{
    protected static $params = [];
    protected static $schemas = [];
    protected static $defaultSchema = DataSchema::class;

    public function __construct(array $arguments = [])
    {
        $this->createFromArray($arguments);
    }

    public function request()
    {
        $method = $this->method();
        $url = [
            Client::$config->getUrl() . $this->url(),
            $this->urlParams(),
        ];
        $body = $this->body();
        $headers = $this->headers();

        $response = Client::query($method, $url, $body, $headers);

        return $this->buildResponse($response);
    }

    public static function build($arguments = [])
    {
        $class = get_called_class();

        return (new $class($arguments))->request();
    }

    public function getRules()
    {
        return $this->rules;
    }

    protected function buildResponse(Response $response)
    {
        if (is_array(static::$schemas) and !empty(static::$schemas)) {
            if (array_key_exists(intval($response->getResponseCode()), static::$schemas)) {
                $asArray = false;

                $schemaClass = $this->matchSchema(intval($response->getResponseCode()), $asArray);

                if ($schemaClass) {
                    return $schemaClass::build($response, $asArray);
                }
            }

            if (array_key_exists(intdiv(intval($response->getResponseCode()), 100), static::$schemas)) {
                $asArray = false;

                $schemaClass = $this->matchSchema(intdiv(intval($response->getResponseCode()), 100), $asArray);

                if ($schemaClass) {
                    return $schemaClass::build($response, $asArray);
                }
            }
        }

        return static::$defaultSchema::build($response);
    }

    protected function matchSchema($code, &$asArray = false)
    {
        if (array_key_exists($code, static::$schemas)) {
            $schemaClass = static::$schemas[$code];

            if (is_array($schemaClass)) {
                $schemaClass = array_shift($schemaClass);

                $asArray = true;
            }

            return $schemaClass;
        }

        return false;
    }

    protected function method()
    {
        return Request::GET;
    }

    protected function url($url = '')
    {
        return $url;
    }

    protected function urlParams()
    {
        $result = [];

        if (is_array(static::$params) and !empty(static::$params)) {
            $properties = get_object_vars($this);

            if (is_array($properties) and !empty($properties)) {
                foreach ($properties as $property => $value) {
                    if (in_array($property, static::$params)) {
                        $result[$property] = $value;
                    }
                }
            };
        }

        return $result;
    }

    protected function body()
    {
        $result = [];

        if (is_array(static::$params) and !empty(static::$params)) {
            $properties = get_object_vars($this);

            if (is_array($properties) and !empty($properties)) {
                foreach ($properties as $property => $value) {
                    if (!in_array($property, static::$params)) {
                        $result[$property] = $value;
                    }
                }
            };
        }

        return $result;
    }

    protected function headers()
    {
        return [];
    }

    protected function createFromArray(array $fields = [])
    {
        parent::createFromArray($fields);
    }
}
