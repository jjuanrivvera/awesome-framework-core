<?php

/**
 * Request class
 * @package Awesome
 */

namespace Awesome;

class Request
{
    protected $routeParams = [];

    protected $path;

    /**
     * Get route params
     * @return array
     */
    public function getRouteParams()
    {
        return $this->routeParams;
    }


    /**
     * Set route params
     * @param array $routeParams
     * @return void
     */
    public function setRouteParams(array $routeParams)
    {
        unset($routeParams['controller']);
        unset($routeParams['action']);

        $this->routeParams = $routeParams;

        foreach ($this->routeParams as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }
}
