<?php

namespace mmaurice\apigate\types;

class UuidType extends \mmaurice\apigate\types\MaskedType
{
    protected $options = [
        'mask' => '/^([0-9a-f]{8}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{12})$/i',
    ];
}
