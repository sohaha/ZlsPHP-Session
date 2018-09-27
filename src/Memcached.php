<?php

namespace Zls\Session;

use z;

/**
 * Memcached托管
 * @author      影浅-Seekwe
 * @email       seekwe@gmail.com
 * Date:        17/2/3
 * Time:        19:50
 * return new \Zls\Session\Memcached(['path' => '127.0.0.1:11211?persistent=0&timeout=3']);
 */
class Memcached extends \Zls_Session
{
    public function init($sessionId)
    {
        ini_set('session.save_handler', 'memcached');
        ini_set('session.save_path', $this->config['path']);
    }


    public function swooleInit($sessionId)
    {
        z::throwIf(true, 500, 'Swoole mode is not supported at this time');
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


    public function open($path, $name)
    {

    }

    public function close()
    {

    }

    public function read($key)
    {

    }

    public function write($key, $val)
    {

    }

    public function destroy($key)
    {

    }

    public function gc($maxlifetime)
    {

    }
}
