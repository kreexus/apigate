<?php

namespace mmaurice\apigate\classes;

use \mmaurice\apigate\configs\Config;
use \mmaurice\apigate\exceptions\Exception;
use \mmaurice\qurl\Client as QurlClient;
use \mmaurice\qurl\Request as QurlRequest;
use \mmaurice\qurl\Response as QurlResponse;
use \ReflectionClass;

abstract class Client
{
    public static $appNamespace;
    public static $namespace;
    public static $config;
    public static $client;

    protected static $instance;

    protected function __construct()
    {
        self::$config = new Config;
        self::$client = new QurlClient;

        self::$appNamespace = (new ReflectionClass($this))->getNamespaceName();
    }

    protected function __clone()
    {
        return false;
    }

    public function __wakeup()
    {
        throw new Exception("Cannot unserialize a singleton.");
    }

    public static function init()
    {
        if (is_null(self::$instance)) {
            self::$instance = new static;
        }

        return self::$instance;
    }

    public function config(Config $config)
    {
        self::$config = $config;
    }

    public static function query($method, $url, $body = [], $headers = [])
    {
        return self::$client
            ->request()
            ->query($method, $url, $body, $headers);
    }

    public static function get($url, $body = [], $headers = [])
    {
        return self::query(QurlRequest::GET, $query, $body, $headers);
    }

    public static function post($url, $body = [], $headers = [])
    {
        return self::query(QurlRequest::POST, $query, $body, $headers);
    }

    public static function put($url, $body = [], $headers = [])
    {
        return self::query(QurlRequest::PUT, $query, $body, $headers);
    }

    public static function head($url, $body = [], $headers = [])
    {
        return self::query(QurlRequest::HEAD, $query, $body, $headers);
    }

    public static function delete($url, $body = [], $headers = [])
    {
        return self::query(QurlRequest::DELETE, $query, $body, $headers);
    }

    public static function connect($url, $body = [], $headers = [])
    {
        return self::query(QurlRequest::CONNECT, $query, $body, $headers);
    }

    public static function options($url, $body = [], $headers = [])
    {
        return self::query(QurlRequest::OPTIONS, $query, $body, $headers);
    }

    public static function path($url, $body = [], $headers = [])
    {
        return self::query(QurlRequest::PATH, $query, $body, $headers);
    }

    public static function trace($url, $body = [], $headers = [])
    {
        return self::query(QurlRequest::TRACE, $query, $body, $headers);
    }

    public static function search($url, $body = [], $headers = [])
    {
        return self::query(QurlRequest::SEARCH, $query, $body, $headers);
    }

    public static function callMethod($name, $arguments = [])
    {
        if (self::hasMethod($name)) {
            $method = self::getMethod($name, $arguments);

            return $method;
        }

        throw new Exception("Method \"{$name}\" not found!");
    }

    protected static function getMethod($name, $arguments = [])
    {
        $methodClassName = self::getMethodClassName($name);

        return $methodClassName::build($arguments);
    }

    protected static function hasMethod($name)
    {
        $methodClassName = self::getMethodClassName($name);

        if (class_exists($methodClassName)) {
            return true;
        }

        return false;
    }

    protected static function getMethodClassName($name)
    {
        $namespace = '\\' . self::$appNamespace . '\\methods\\' . ((pathinfo($name)['dirname'] !== '.') ? pathinfo($name)['dirname'] . '\\' : '') . ucfirst(pathinfo($name)['basename']) . 'Method';

        $namespace = str_replace('/', '\\', $namespace);

        return $namespace;
    }
}
