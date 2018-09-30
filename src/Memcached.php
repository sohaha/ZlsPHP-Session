<?php

namespace Zls\Session;

/**
 * Memcached托管
 * @author      影浅-Seekwe
 * @email       seekwe@gmail.com
 * Date:        17/2/3
 * Time:        19:50
 * return new \Zls\Session\Memcached(['path' => '127.0.0.1:11211?persistent=0&timeout=3']);
 */
class Memcached extends Session
{
    public function init($sessionId)
    {
        ini_set('session.save_handler', 'memcached');
        ini_set('session.save_path', $this->config['path']);
    }

}
