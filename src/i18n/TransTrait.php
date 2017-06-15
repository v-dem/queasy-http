<?php

namespace queasy\i18n;

trait TransTrait
{

    protected static function trans($key, array $vars = null, $lang = null)
    {
        return Trans::getInstance()->trans(__CLASS__ . '.' . $key, $vars, $lang);
    }

}

