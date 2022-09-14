<?php

namespace Awesome;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

/*
* View
* @package Awesome
*/
class View
{
    /**
     * Default views path
     */
    private const DEFAULT_VIEWS_PATH = '../App/Views';

    /**
     * View template
     */
    protected static $template;

    /**
     * Template engine instance
     * @var Environment
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
            $loader = new FilesystemLoader(self::DEFAULT_VIEWS_PATH);
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
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
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
        try {
            return self::render();
        } catch (LoaderError $e) {
        } catch (RuntimeError $e) {
        } catch (SyntaxError $e) {
        }
    }
}
