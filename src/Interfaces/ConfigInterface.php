<?php

namespace Awesome\Interfaces;

/**
 * Interface ConfigInterface
 * @package Awesome\Interfaces
 * @author Juan Felipe Rivera G
 */
interface ConfigInterface
{
    /**
     * Get config value
     * @param string $key Config key
     * @return mixed
     */
    public function get(string $key): mixed;

    /**
     * Get all config values
     * @return array<mixed>
     */
    public function all(): array;

    /**
     * Load the config files into the params array
     * @return void
     */
    public function loadConfig(): void;

    /**
     * Get config path
     * @return string
     */
    public function getConfigPath(): string;

    /**
     * Set config path
     * Note: This method will unset all previous config params and load new ones
     * @param string $configPath
     * @return void
     */
    public function setConfigPath(string $configPath): void;
}
