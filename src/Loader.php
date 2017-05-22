<?php

namespace queasy;

use queasy\config\ConfigTrait;

class Loader
{

    use ConfigTrait;

    public static function load($classKey)
    {
        return self::config()->need($classKey);
    }

}

