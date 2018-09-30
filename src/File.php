<?php

namespace Zls\Session;

use swoole_serialize;
use z;

/**
 * File托管
 * @author        影浅
 * @email         seekwe@gmail.com
 * @copyright     Copyright (c) 2015 - 2017, 影浅, Inc.
 * @link          ---
 * @since         v0.0.1
 * @updatetime    2018-09-26 16:15:46
 */
class File extends \Zls_Session
{
    private $dir  = '';
    private $file = '';
    private $data = [];


    public function __construct($configFileName = '')
    {
        parent::__construct($configFileName);
        $this->config = z::config()->getSessionConfig();
        $this->dir = z::realPathMkdir('/tmp/ZlsSwooleSession', true);
    }

    public function init($id)
    {

    }

    public function swooleInit($id)
    {
        $this->file = $this->dir . $id;
        $_SESSION = $this->swooleRead($id);
    }

    public function swooleRead($id)
    {
        if (file_exists($this->file)) {
            /** @noinspection PhpVoidFunctionResultUsedInspection */
            $data = swoole_serialize::unpack(file_get_contents($this->file));
        } else {
            $data = [];
        }

        return $data;
    }

    public function swooleDestroy($id)
    {
        @unlink($this->file);
    }

    public function swooleWrite($id, $data)
    {
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $this->data = swoole_serialize::pack($data);
        file_put_contents($this->file, $this->data);
    }

    public function swooleGc($maxlifetime = 0)
    {
        $handle = opendir($this->dir);
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != "..") {
                $file = $this->dir . $file;
                if (!is_file($file)) {
                    continue;
                }
                $lastUpdatedAt = filemtime($file);
                if (time() - $lastUpdatedAt > $maxlifetime) {
                    unlink($file);
                }
            }
        }
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
