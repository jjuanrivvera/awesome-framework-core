<?php

namespace Awesome\Interfaces;

use DI\FactoryInterface;
use Psr\Container\ContainerInterface as ParentContainerInterface;

/**
 * Interface ContainerInterface
 * @package Awesome\Interfaces
 * @author Juan Felipe Rivera G
 */
interface ContainerInterface extends ParentContainerInterface, FactoryInterface
{
    public function set(string $id, mixed $value): void;
}
