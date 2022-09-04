<?php

namespace Awesome;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/*
* View
* @package Awesome
*/
class View
{
    /**
     * View template
     */
    protected static $template;

    /**
     * Template engine instance
     */
    protected static $engine;

    /**
     * Template variables
     */
    protected static $data = [];
    
    /**
     * Render a view template using Twig
     *
     * @param string $template The template file
     * @param array $args Associative array of data to display in the view (optional)
     *
     * @return View
     */
    public static function make($template, $args = [])
    {
        static $twig = null;
        
        if ($twig === null) {
            $loader = new FilesystemLoader('../App/Views');
            $twig = new Environment($loader);
        }
        
        self::$engine = $twig;
        self::$template = $template;
        self::$data = $args;

        return new self();
    }

    /**
     * Render a view template using Engine
     *
     * @return mixed
     */
    public static function render()
    {
        return self::$engine->render(self::$template, self::$data);
    }

    /**
     * Cast view class to string
     */
    public function __toString()
    {
        return self::render();
    }
}
