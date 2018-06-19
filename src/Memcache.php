<?php

namespace Zls\Session;

/**
 * Memcache托管
 * @author      影浅-Seekwe
 * @email       seekwe@gmail.com
 * Date:        17/2/3
 * Time:        19:50
 * return new \Zls\Session\Memcache(['path' => 'tcp://127.0.0.1:11211?persistent=0&timeout=3']);
 */
class Memcache extends \Zls_Session
{
    public function init($sessionID)
    {
        ini_set('session.save_handler', 'memcache');
        ini_set('session.save_path', $this->config['path']);
    }

    public function swooleInit($sessionId)
    {
        $_SESSION = [];
    }

    public function swooleWrite($sessionId, $sessionData)
    {
    }

    public function swooleRead($sessionId)
    {
    }

    public function swooleDestroy($sessionId)
    {
    }

    public function swooleGc($maxlifetime = 0)
    {
    }

}
