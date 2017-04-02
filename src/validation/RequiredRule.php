<?php

namespace queasy\validation;

class RequiredRule extends Rule
{

    public function validate($value)
    {
        return !($this->getParameter()
            && (is_null($value)
                || (0 === strlen((string) $value))));
    }

}

