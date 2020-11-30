<?php

namespace mmaurice\apigate\builders;

use \mmaurice\apigate\Client;
use \mmaurice\apigate\schemas\DataSchema;
use \mmaurice\qurl\Request as QurlRequest;
use \mmaurice\qurl\Response as QurlResponse;

abstract class MethodBuilder extends \mmaurice\apigate\classes\Schema
{
    const GET = QurlRequest::GET;
    const POST = QurlRequest::POST;
    const PUT = QurlRequest::PUT;
    const HEAD = QurlRequest::HEAD;
    const DELETE = QurlRequest::DELETE;
    const CONNECT = QurlRequest::CONNECT;
    const OPTIONS = QurlRequest::OPTIONS;
    const PATH = QurlRequest::PATH;
    const TRACE = QurlRequest::TRACE;
    const SEARCH = QurlRequest::SEARCH;

    protected static $params = [];
    protected static $schemas = [];
    protected static $defaultSchema = DataSchema::class;

    public function __construct(array $arguments = [])
    {
        $this->createFromArray($arguments);
    }

    public function request()
    {
        $response = Client::query($this->method(), [
            Client::$config->getUrl() . $this->url(),
            $this->urlParams(),
        ], $this->body(), $this->headers());

        return $this->buildResponse($response);
    }

    public static function build($arguments = [])
    {
        $class = get_called_class();

        return (new $class($arguments))->request();
    }

    public function getRules()
    {
        return self::$rules;
    }

    protected function buildResponse(QurlResponse $response)
    {
        if (is_array(static::$schemas) and !empty(static::$schemas)) {
            if (array_key_exists(intval($response->getResponseCode()), static::$schemas)) {
                $asArray = false;

                $schema = $this->matchSchema(intval($response->getResponseCode()), $asArray);

                if ($schema) {
                    return $schema::build($response, $asArray);
                }
            }

            if (array_key_exists(intdiv(intval($response->getResponseCode()), 100), static::$schemas)) {
                $asArray = false;

                $schema = $this->matchSchema(intdiv(intval($response->getResponseCode()), 100), $asArray);

                if ($schema) {
                    return $schema::build($response, $asArray);
                }
            }
        }

        return static::$defaultSchema::build($response);
    }

    protected function matchSchema($code, &$asArray = false)
    {
        if (array_key_exists($code, static::$schemas)) {
            $schema = static::$schemas[$code];

            if (is_array($schema)) {
                $schema = array_shift($schema);

                $asArray = true;
            }

            return $schema;
        }

        return false;
    }

    protected function method()
    {
        return self::GET;
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
        } else {
            $result = get_object_vars($this);
        }

        return $result;
    }

    protected function headers()
    {
        return [];
    }
}
