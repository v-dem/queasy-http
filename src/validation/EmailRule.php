<?php

namespace queasy\validation;

class EmailRule extends Rule
{

    public function validate($value)
    {
        return $this->getParameter() && filter_var($value, FILTER_VALIDATE_EMAIL);
    }

}

