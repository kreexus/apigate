<?php

namespace mmaurice\apigate\components;

use \mmaurice\apigate\configs\Config;
use \mmaurice\apigate\exceptions\Exception;
use \mmaurice\qurl\Client;
use \mmaurice\qurl\Request;
use \mmaurice\qurl\Response;
use \ReflectionClass;

abstract class ClientComponent
{
    public static $appNamespace;
    public static $namespace;
    public static $config;
    public static $client;

    protected static $instance;

    protected function __construct()
    {
        self::$config = new Config;
        self::$client = new Client;

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
        return self::query(Request::GET, $query, $body, $headers);
    }

    public static function post($url, $body = [], $headers = [])
    {
        return self::query(Request::POST, $query, $body, $headers);
    }

    public static function put($url, $body = [], $headers = [])
    {
        return self::query(Request::PUT, $query, $body, $headers);
    }

    public static function head($url, $body = [], $headers = [])
    {
        return self::query(Request::HEAD, $query, $body, $headers);
    }

    public static function delete($url, $body = [], $headers = [])
    {
        return self::query(Request::DELETE, $query, $body, $headers);
    }

    public static function connect($url, $body = [], $headers = [])
    {
        return self::query(Request::CONNECT, $query, $body, $headers);
    }

    public static function options($url, $body = [], $headers = [])
    {
        return self::query(Request::OPTIONS, $query, $body, $headers);
    }

    public static function path($url, $body = [], $headers = [])
    {
        return self::query(Request::PATH, $query, $body, $headers);
    }

    public static function trace($url, $body = [], $headers = [])
    {
        return self::query(Request::TRACE, $query, $body, $headers);
    }

    public static function search($url, $body = [], $headers = [])
    {
        return self::query(Request::SEARCH, $query, $body, $headers);
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
