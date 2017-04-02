<?php

namespace queasy\i18n;

trait LangTrait
{

    protected static function trans($key, array $vars = null, $lang = null)
    {
        return Lang::getInstance()->trans(__CLASS__ . '.' . $key, $vars, $lang);
    }

}

