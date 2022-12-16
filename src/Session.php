<?php

namespace Awesome;

class Session
{
    /**
     * Starts new or resumes existing session
     * @access  public
     * @return  bool
     */
    public function start()
    {
        if (session_start()) {
            return true;
        }

        return false;
    }

    /**
     * End existing session, destroy, unset and delete session cookie
     * @access  public
     * @return  void
     */
    public function end()
    {
        if ($this->status != true) {
            $this->start();
        }

        session_destroy();
        session_unset();
        setcookie(session_name(), null, 0, "/");
    }

    /**
     * Set new session item
     * @access  public
     * @param   mixed
     * @param   mixed
     * @return  mixed
     */
    public function set($key, $value)
    {
        return $_SESSION[$key] = $value;
    }

    /**
     * Checks if session key is already set
     * @access  public
     * @param   mixed  - session key
     * @return  bool
     */
    public function has($key)
    {
        if (isset($_SESSION[$key])) {
            return true;
        }

        return false;
    }

    /**
     * Get session item
     * @access  public
     * @param   mixed
     * @return  mixed
     */
    public function get($key)
    {
        if (!isset($_SESSION[$key])) {
            return false;
        }

        return $_SESSION[$key];
    }
}
