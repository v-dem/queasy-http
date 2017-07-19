<?php

namespace queasy;

interface IRoute
{

    public function resolve(array $route = null);

    public function get();

}

