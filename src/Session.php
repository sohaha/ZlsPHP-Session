<?php

namespace Zls\Session;

use Z;

/**
 * Session
 * @author        影浅
 * @email         seekwe@gmail.com
 * @copyright     Copyright (c) 2015 - 2017, 影浅, Inc.
 * @link          ---
 * @since         v0.0.1
 * @updatetime    2018-09-30 16:27
 */
class Session extends \Zls_Session
{
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

    public function init($id)
    {
    }
}
