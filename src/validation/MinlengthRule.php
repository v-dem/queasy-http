<?php

namespace queasy\validation;

class MinlengthRule extends Rule
{

    public function validate($value)
    {
        return strlen((string) $value) >= $this->getParameter();
    }

}

