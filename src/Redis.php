<?php

namespace Zls\Session;

/**
 * Redis托管
 * @author        影浅
 * @email         seekwe@gmail.com
 * @copyright     Copyright (c) 2015 - 2017, 影浅, Inc.
 * @link          ---
 * @since         v0.0.1
 * @updatetime    2018-1-26 18:04:33
 * return new \Zls\Session\Redis(['path' => 'tcp://127.0.0.1:6379?timeout=3&persistent=0']);
 */
use Z;

class Redis extends \Zls_Session
{
    private $sessionHandle;
    private $sessionConfig = [];
    private $config = [];

    public function init()
    {
        ini_set('session.save_handler', 'redis');
        ini_set('session.save_path', $this->config['path']);
    }

    public function swooleInit($sessionId)
    {
        $sessionConfig = Z::config()->getSessionConfig();
        $path = $this->config['path'];
        $config = [
            'class'  => '\Zls\Cache\Redis',
            'config' => [],
        ];
        $masters = \explode(',', $path);
        foreach ($masters as $k => $master) {
            $parseUrl = parse_url($master);
            $auth = z::arrayGet($parseUrl, 'auth');
            $config['config'][] = [
                'master' =>
                    [
                        'type'     => 'tcp',
                        'prefix'   => z::arrayGet($parseUrl, 'prefix', 'ZLSESSION'),
                        'sock'     => '',
                        'host'     => z::arrayGet($parseUrl, 'host', '127.0.0.1'),
                        'port'     => z::arrayGet($parseUrl, 'port', 6379),
                        'password' => $auth ? \urldecode($auth) : null,
                        'timeout'  => z::arrayGet($parseUrl, 'timeout', 3) * 1000,
                        'retry'    => z::arrayGet($parseUrl, 'retry', 100),
                        'db'       => z::arrayGet($parseUrl, 'database', 0),
                    ],
                'slaves' => [],
            ];
        }
        $this->sessionHandle = Z::cache($config);
        $this->sessionConfig = $sessionConfig;
        $_SESSION = $this->swooleRead($sessionId) ?: [];
    }

    public function swooleRead($sessionId)
    {
        z::setCookieRaw($this->sessionConfig['session_name'], $sessionId, $this->sessionConfig['lifetime'] + time(), '/');

        return unserialize($this->sessionHandle->get($sessionId));
    }

    public function swooleDestroy($sessionId)
    {
        return $this->sessionHandle->delete($sessionId);
    }

    public function swooleWrite($sessionId, $sessionData)
    {

        z::setCookieRaw($this->sessionConfig['session_name'], $sessionId, $this->sessionConfig['lifetime'] + time(), '/');

        return $this->sessionHandle->set($sessionId, serialize($sessionData), $this->sessionConfig['lifetime']);
    }


    public function swooleGc($maxlifetime = 0)
    {
        return true;
    }

}
