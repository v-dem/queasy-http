<?php

namespace queasy\validation;

abstract class Rule
{

    private $validator;
    private $parameter;

    public function __construct(Validator $validator, $parameter)
    {
        $this->validator = $validator;
        $this->parameter = $parameter;
    }

    abstract public function validate($value);

    protected function getParameter()
    {
        return $this->parameter;
    }

}

