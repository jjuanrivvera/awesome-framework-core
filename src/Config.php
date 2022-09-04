<?php

namespace Awesome;

class Config
{
    /**
     * Connection driver
     */
    protected $driver;

    /**
     * Database host
     */
    protected $dbHost;

    /**
     * Database name
     */
    protected $dbName;

    /**
     * Database user
     */
    protected $dbUser;

    /**
     * Database password
     */
    protected $dbPassword;

    /**
     * Database port
     */
    protected $dbPort;

    /**
     * Database connection string
     */
    protected $connectionString;

    public function getDbHost()
    {
        return $this->dbHost;
    }

    public function setDbHost($dbHost)
    {
        $this->dbHost = $dbHost;
    }

    public function getDbName()
    {
        return $this->dbName;
    }

    public function setDbName($dbName)
    {
        $this->dbName = $dbName;
    }

    public function getDbUser()
    {
        return $this->dbUser;
    }

    public function setDbUser($dbUser)
    {
        $this->dbUser = $dbUser;
    }

    public function getDbPassword()
    {
        return $this->dbPassword;
    }

    public function setDbPassword($dbPassword)
    {
        $this->dbPassword = $dbPassword;
    }

    public function getDbPort()
    {
        return $this->dbPort;
    }

    public function setDbPort($dbPort)
    {
        $this->dbPort = $dbPort;
    }

    public function getDriver()
    {
        return $this->driver;
    }

    public function setDriver($driver)
    {
        $this->driver = $driver;
    }

    public function getConnectionString()
    {
        return $this->connectionString;
    }

    public function setConnectionString($connectionString)
    {
        $this->connectionString = $connectionString;
    }

    public function addConfigValue($key, $value)
    {
        $this->{$key} = $value;
    }

    public function __construct()
    {
        $configParams = [];

        foreach (glob(__DIR__ . '/../config/*.php') as $filename) {
            $configParams = array_merge($configParams, include $filename);
        }

        foreach ($configParams as $key => $value) {
            $this->{$key} = $value;
        }
    }
}
