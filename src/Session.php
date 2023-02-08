<?php

namespace Awesome;

class Session
{
    /**
     * Session status
     * @var bool
     */
    protected bool $status = false;

    /**
     * Starts new or resumes existing session
     * @return bool
     */
    public function start()
    {
        if ($this->status == true) {
            return true;
        }

        $this->status = session_start();

        return $this->status;
    }

    /**
     * End existing session, destroy, unset and delete session cookie
     * @return void
     */
    public function end()
    {
        if ($this->status == false) {
            return;
        }

        session_unset();
        session_destroy();
        setcookie(session_name(), '', time() - 3600);

        $this->status = false;
    }

    /**
     * Set new session item
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public function set(string $key, $value)
    {
        return $_SESSION[$key] = $value;
    }

    /**
     * Checks if session key is already set
     * @param string $key
     * @return bool
     */
    public function has(string $key)
    {
        if (isset($_SESSION[$key])) {
            return true;
        }

        return false;
    }

    /**
     * Get session item
     * @param string $key
     * @return mixed
     */
    public function get(string $key)
    {
        if (!isset($_SESSION[$key])) {
            return false;
        }

        return $_SESSION[$key];
    }
}
