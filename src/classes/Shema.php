<?php

namespace mmaurice\apigate\classes;

use \mmaurice\apigate\Client;
use \mmaurice\apigate\classes\Format;
use \mmaurice\apigate\exceptions\ShemaException;

abstract class Shema
{
    const RULE_TYPE_KEY = 0;
    const RULE_REQUIRED_KEY = 1;
    const RULE_DEFAULT_KEY = 2;

    const RULE_FORMAT = 0;
    const RULE_FORMAT_KEY = 1;
    const RULE_FORMAT_OPTIONS = 2;

    /**
     * Набор правил фильтрации переменных
     * 
     * Пример:
     * 
     * $rules = [
     *     'var1' => ['string'],
     *     'var2' => [['double', 'NumberType']],
     *     'var3' => [['string', 'enumFormat', ['enum' => [self::STATUS_ONLINE, self::STATUS_OFFLINE]]]],
     *     'var4' => ['integer', true],
     *     'var5' => ['double', false, 1.5],
     *     'var6' => [NewOrderShema::class],
     * ];
     * 
     * var1 - переменная типа "string"
     * var2 - переменная типа "double", которая будет проверена через класс-валидатор NumberType
     * var3 - переменная типа "string", которая будет проверена через класс-валидатор EnumFormat, к которому будет применен следующий за этим массив опций
     * var4 - переменная типа "integer", указание которой является обязательным
     * var5 - переменная типа "double", указание которой не является обязательным, но которая имеет значение по-умолчанию, равное 1.5
     * var6 - переменная, содержимое которой будет эквивалентно схеме, описанной в классе NewOrderShema
     *
     * @var array
     */
    protected static $rules = [];

    public function __toString()
    {
        return json_encode(get_object_vars($this));
    }

    public function export()
    {
        return get_object_vars($this);
    }

    protected function createFromArray(array $fields = [])
    {
        $fieldsNames = array_keys($fields);

        if (is_array(static::$rules) and !empty(static::$rules)) {
            foreach (static::$rules as $fieldName => $rule) {
                if (array_key_exists(self::RULE_REQUIRED_KEY, $rule) and ($rule[self::RULE_REQUIRED_KEY] === true) and !in_array($fieldName, $fieldsNames)) {
                    throw new ShemaException("Imported data not have required field '{$fieldName}'.");
                }

                if (!array_key_exists(self::RULE_TYPE_KEY, $rule)) {
                    throw new ShemaException("Type rule for field '{$fieldName}' is not defined.");
                }

                if (array_key_exists($fieldName, $fields)) {
                    $type = (is_array($rule[self::RULE_TYPE_KEY]) ? (array_key_exists(self::RULE_FORMAT, $rule[self::RULE_TYPE_KEY]) ? $rule[self::RULE_TYPE_KEY][self::RULE_FORMAT] : $rule[self::RULE_TYPE_KEY]) : $rule[self::RULE_TYPE_KEY]);
                    $formatMethod = (is_array($rule[self::RULE_TYPE_KEY]) ? (array_key_exists(self::RULE_FORMAT_KEY, $rule[self::RULE_TYPE_KEY]) ? $rule[self::RULE_TYPE_KEY][self::RULE_FORMAT_KEY] : null) : null);
                    $formatOptions = (is_array($rule[self::RULE_TYPE_KEY]) ? (array_key_exists(self::RULE_FORMAT_OPTIONS, $rule[self::RULE_TYPE_KEY]) ? $rule[self::RULE_TYPE_KEY][self::RULE_FORMAT_OPTIONS] : []) : []);

                    if ($type === 'float') {
                        $type = 'double';
                    }

                    $this->$fieldName = $fields[$fieldName];

                    if (!array_key_exists(self::RULE_DEFAULT_KEY, $rule) or ($this->$fieldName !== $rule[self::RULE_DEFAULT_KEY])) {
                        if (!$this->checkFormat($this->$fieldName, $type, $formatMethod, $formatOptions)) {
                            throw new ShemaException("Field \"{$fieldName}\" is not a \"{$formatMethod}\".");
                        }
                    }
                } else {
                    if (array_key_exists(self::RULE_DEFAULT_KEY, $rule)) {
                        $this->$fieldName = $rule[self::RULE_DEFAULT_KEY];
                    }
                }
            }
        }
    }

    protected function checkFormat(&$value, $type, $formatMethod = null, $formatOptions = [])
    {
        if (!is_null($formatMethod)) {
            if (!class_exists($formatMethod)) {
                throw new ShemaException("Validator method '{$formatMethod}' is not found.");
            }

            $formatMethodClass = new $formatMethod(array_merge([
                'type' => $type,
            ], $formatOptions));

            if (!($formatMethodClass instanceof Format)) {
                throw new ShemaException("Validator method '{$formatMethod}' is not instanced of Format.");
            }

            return $formatMethodClass->valide($value, function ($value, $options = []) {
                return $this->checkType($value, $options['type']);
            });
        } else {
            $value = $this->checkType($value, $type);

            return true;
        }
    }

    protected function checkType($value, $type)
    {
        if (class_exists($type)) {
            if ($value instanceof Shema) {
                $value = $value->export();
            }

            if (is_null($value) or empty($value)) {
                $value = [];
            }

            return new $type($value);
        } else {
            if (!settype($value, $type)) {
                throw new ShemaException("Wrong type '{$type}'.");
            }

            if (gettype($value) !== $type) {
                throw new ShemaException("Wrong type '{$type}'.");
            }

            return $value;
        }
    }
}
