<?php

namespace queasy\config;

trait ConfigTrait
{

    protected static function config()
    {
        $className = __CLASS__;

        return Provider::getInstance()->$className;
    }

}

