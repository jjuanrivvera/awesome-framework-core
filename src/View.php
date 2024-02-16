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
    protected static string $template;

    /**
     * Template engine instance
     * @var Environment
     */
    protected static Environment $engine;

    /**
     * Template variables
     * @var array<mixed>
     */
    protected static array $data = [];

    /**
     * Render a view template using Twig
     * @param string $template The template file
     * @param array<mixed> $args Associative array of data to display in the view (optional)
     * @return View
     */
    public static function make(string $template, array $args = []): View
    {
        static $twig = null;

        if ($twig === null) {
            $app = App::getInstance();
            $loader = new FilesystemLoader($app->getViewPath());
            $twig = new Environment($loader);
        }

        self::$engine = $twig;
        self::$template = $template;
        self::$data = $args;

        return new self();
    }

    /**
     * Render a view template using Engine
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public static function render(): string
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
        } catch (LoaderError | RuntimeError | SyntaxError $e) {
        }

        return $return;
    }

    /**
     * View exists
     * @param string $template
     * @return bool
     */
    public static function exists(string $template): bool
    {
        $app = App::getInstance();
        return file_exists($app->getViewPath() . "/$template");
    }
}
