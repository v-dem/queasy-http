<?php

namespace queasy;

interface RouteInterface
{

    public function resolve(array $route = null);

    public function get();

}

