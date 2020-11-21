<?php

namespace mmaurice\apigate\builders;

use \mmaurice\apigate\Client;
use \mmaurice\apigate\schemas\DataSchema;
use \mmaurice\qurl\Request;
use \mmaurice\qurl\Response;

class MethodBuilder extends \mmaurice\apigate\components\SchemaComponent
{
    protected $schemas = [];

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
        if (is_array($this->schemas) and !empty($this->schemas)) {
            $schemaClass = null;

            if (array_key_exists(intval($response->getResponseCode()), $this->schemas)) {
                $schemaClass = $this->schemas[intval($response->getResponseCode())];
            }

            if (array_key_exists(intdiv(intval($response->getResponseCode()), 100), $this->schemas)) {
                $schemaClass = $this->schemas[intdiv(intval($response->getResponseCode()), 100)];
            }

            if (!is_null($schemaClass)) {
                return $schemaClass::build($response, $asArray);
            }
        }

        return DataSchema::build($response, $asArray);
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
        return [];
    }

    protected function body()
    {
        return get_object_vars($this);
    }

    protected function headers()
    {
        return [];
    }
}
