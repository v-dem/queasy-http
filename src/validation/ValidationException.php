<?php

namespace queasy\validation;

use queasy\ApplicationException;

class ValidationException extends ApplicationException
{

    private $errors;

    public function __construct(array $errors = array())
    {
        $this->errors = $errors;
    }

    public function getErrors()
    {
        return $this->errors;
    }

}

