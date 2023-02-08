<?php

namespace Awesome;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

/**
* Class View
* @package Awesome
*/
class View
{
    /**
     * View template
     * @var string
     */
    protected static $template;

    /**
     * Template engine instance
     * @var Environment
     */
    protected static $engine;

    /**
     * Template variables
     * @var array<mixed>
     */
    protected static $data = [];

    /**
     * Render a view template using Twig
     * @param string $template The template file
     * @param array<mixed> $args Associative array of data to display in the view (optional)
     * @return View
     */
    public static function make(string $template, array $args = [])
    {
        static $twig = null;

        if ($twig === null) {
            $loader = new FilesystemLoader(App::getViewPath());
            $twig = new Environment($loader);
        }

        self::$engine = $twig;
        self::$template = $template;
        self::$data = $args;

        return new self();
    }

    /**
     * Render a view template using Engine
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
     * @return string
     */
    public function __toString()
    {
        $return = '';

        try {
            $return = self::render();
        } catch (LoaderError $e) {
        } catch (RuntimeError $e) {
        } catch (SyntaxError $e) {
        }

        return $return;
    }

    /**
     * View exists
     * @param string $template
     * @return bool
     */
    public static function exists($template)
    {
        return file_exists(App::getViewPath() . "/$template");
    }
}
