<?php

namespace mmaurice\apigate\components;

use \mmaurice\apigate\exceptions\Exception;

class StorageComponent
{
    protected $options = [];

    public function __call($name, $arguments = [])
    {
        $methodType = $this->matchMethodType($name);
        $fieldName = $this->matchMethod($name);

        switch ($methodType) {
            case 'get':
                return $this->get($fieldName, array_shift($arguments));
            break;
            case 'set':
                return $this->set($fieldName, array_shift($arguments));
            break;
            case 'push':
                return $this->push($fieldName, array_shift($arguments));
            break;
            case 'pushAssoc':
                return $this->pushAssoc($fieldName, array_shift($arguments), array_shift($arguments));
            break;
            case 'getAssoc':
                return $this->getAssoc($fieldName, array_shift($arguments), array_shift($arguments));
            break;
            case 'has':
                return $this->has($fieldName);
            break;
            case 'drop':
                return $this->drop($fieldName);
            break;
            default:
                throw new Exception("Call unknown method \"$name\"");
            break;
        }
    }

    public function get($name, $default = null)
    {
        if ($this->hasOption($name)) {
            return $this->getOption($name, $default);
        }
    }

    public function set($name, $value)
    {
        $this->setOption($name, $value);

        return $this;
    }

    public function has($name)
    {
        return $this->hasOption($name);
    }

    public function push($name, $value)
    {
        $this->pushOption($name, $value);

        return $this;
    }

    public function pushAssoc($name, $key, $value)
    {
        $this->pushAssocOption($name, $key, $value);

        return $this;
    }

    public function getAssoc($name, $key, $default = null)
    {
        return $this->getAssocOption($name, $key, $default);
    }

    public function drop($name = null)
    {
        if (is_null($name)) {
            $this->options = [];
        } else {
            $this->deleteOption($name);
        }

        return $this;
    }

    protected function matchMethodType($name)
    {
        if (preg_match('/^(pushAssoc|getAssoc|get|set|drop|has|push).+$/i', trim($name), $matches)) {
            array_shift($matches);

            return $matches[0];
        }
    }

    protected function matchMethod($name)
    {
        if (preg_match('/^(?:pushAssoc|getAssoc|get|set|drop|has|push)(.+)$/i', trim($name), $matches)) {
            array_shift($matches);

            return lcfirst($matches[0]);
        }
    }

    protected function setOptions($options = [])
    {
        if (is_array($options) and !empty($options)) {
            array_map(function ($name, $value) {
                $this->setOption($name, $value);
            }, array_keys($options), array_values($options));
        }

        return $this;
    }

    protected function pushOptions($options = [])
    {
        if (is_array($options) and !empty($options)) {
            array_map(function ($name, $value) {
                $this->pushOption($name, $value);
            }, array_keys($options), array_values($options));
        }

        return $this;
    }

    protected function setOption($name, $value)
    {
        $this->options[$name] = $value;

        return $this;
    }

    protected function pushOption($name, $value)
    {
        if (!is_array($this->options[$name])) {
            if (!is_null($this->options[$name]) and !empty($this->options[$name])) {
                $this->options[$name] = [$this->options[$name]];
            } else {
                $this->options[$name] = [];
            }
        }

        array_push($this->options[$name], $value);

        return $this;
    }

    protected function pushAssocOption($name, $key, $value)
    {
        if (!is_array($this->options[$name])) {
            if (!is_null($this->options[$name]) and !empty($this->options[$name])) {
                $this->options[$name] = [$this->options[$name]];
            } else {
                $this->options[$name] = [];
            }
        }

        $this->options[$name][$key] = $value;

        return $this;
    }

    protected function getAssocOption($name, $key, $default = null)
    {
        if ($this->hasOption($name)) {
            if (array_key_exists($key, $this->options[$name])) {
                return $this->options[$name][$key];
            }
        }

        return $default;
    }

    protected function getOption($name, $default = null)
    {
        if ($this->hasOption($name)) {
            return $this->options[$name];
        }

        return $default;
    }

    protected function deleteOption($name)
    {
        if ($this->hasOption($name)) {
            unset($this->options[$name]);
        }

        return $this;
    }

    protected function hasOption($name)
    {
        if (array_key_exists($name, $this->options)) {
            return true;
        }

        return false;
    }
}
