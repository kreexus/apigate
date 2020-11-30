<?php

namespace mmaurice\apigate\types;

class DateIso8601Type extends \mmaurice\apigate\types\MaskedType
{
    protected $options = [
        'mask' => '/^([\d]{4})\-([\d]+)\-([\d]+)T([\d]+)\:([\d]+)\:([\d]+).*$/i',
    ];
}
