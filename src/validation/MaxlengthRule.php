<?php

namespace queasy\validation;

class MaxlengthRule extends Rule
{

    public function validate($value)
    {
        return strlen((string) $value) <= $this->getParameter();
    }

}

