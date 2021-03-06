<?php

namespace Zls\Session;

use Z;

/**
 * MySQL托管
 * 表结构如下：
 * CREATE TABLE `session_handler_table` (
 * `id` varchar(255) NOT NULL,
 * `data` mediumtext NOT NULL,
 * `timestamp` int(255) NOT NULL,
 * PRIMARY KEY (`id`),
 * UNIQUE KEY `id` (`id`,`timestamp`),
 * KEY `timestamp` (`timestamp`)
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 */
/*
return new \Zls\Session\Mysql(array(
    //如果使用数据库配置里面的组信息，这里可以设置group组名称，没有就留空
    //设置group组名称后，下面连接的配置不再起作用，group优先级大于下面的连接信息
    'group' => '',
     //表全名，不包含前缀
    'table' => 'session_handler_table',
    //表前缀，如果有使用数据库配置组里面的信息
    //这里可以设置相同的数据库配置组里面的前缀才能正常工作
    'table_prefix' => '',
    //连接信息
    'hostname' => '127.0.0.1',
    'port' => 3306,
    'username' => 'root',
    'password' => 'admin',
    'database' => 'test',
	)
);
*/

class Mysql extends Session
{
    protected $dbConfig;
    protected $dbTable;
    protected $db;

    public function __construct($configFileName)
    {
        parent::__construct($configFileName);
        $cfg = Z::config()->getSessionConfig();
        $this->config['lifetime'] = $cfg['lifetime'];
        $this->config['session_name'] = $cfg['session_name'];
        $this->setDbConfig();
    }

    private function setDbConfig()
    {
        $this->dbTable = $this->config['table'];
        if ($this->config['group']) {
            $this->dbConfig = $this->config['group'];
        } else {
            $db = z::factory('Zls_Database_ActiveRecord');
            $dbConfig = $db->getDefaultConfig();
            $dbConfig['database'] = $this->config['database'];
            $dbConfig['tablePrefix'] = $this->config['table_prefix'];
            $dbConfig['masters']['master01']['hostname'] = $this->config['hostname'];
            $dbConfig['masters']['master01']['port'] = $this->config['port'];
            $dbConfig['masters']['master01']['username'] = $this->config['username'];
            $dbConfig['masters']['master01']['password'] = $this->config['password'];
            $this->dbConfig = $dbConfig;
        }
    }

    public function init($sessionId)
    {
        session_set_save_handler([$this, 'open'], [$this, 'close'], [$this, 'read'],
                                 [$this, 'write'], [$this, 'destroy'], [$this, 'gc']
        );
    }

    public function swooleInit($id)
    {
        $_SESSION = $this->swooleRead($id) ?: [];
    }

    public function swooleRead($id)
    {
        return unserialize($this->read($id));
    }

    public function read($id)
    {
        $db = Z::db($this->dbConfig);
        $result = $db->from($this->dbTable)->where(['id' => $id])->execute();
        if ($record = $result->row()) {
            $time = time() + (int)$this->config['lifetime'];
            $data['timestamp'] = $time;
            $where['id'] = $id;
            z::setCookieRaw($this->config['session_name'], $id, $time, '/');
            $db->update($this->dbTable, $data, $where)->execute();

            return $record['data'];
        } else {
            return '';
        }
    }

    public function swooleDestroy($id)
    {
        return $this->destroy($id);
    }

    public function destroy($id)
    {
        unset($_SESSION);
        $db = Z::db($this->dbConfig);

        return $db->delete($this->dbTable, ['id' => $id])->execute() > 0;
    }

    public function swooleWrite($id, $sessionData)
    {
        return $this->write($id, serialize($sessionData));
    }

    public function write($id, $sessionData)
    {
        $time = time() + (int)$this->config['lifetime'];
        $data['id'] = $id;
        $data['data'] = $sessionData;
        $data['timestamp'] = $time;
        z::setCookieRaw($this->config['session_name'], $id, $time, '/');
        $db = Z::db($this->dbConfig);
        $db->replace($this->dbTable, $data);

        return $db->execute() > 0;
    }

    public function swooleGc($maxlifetime = 0)
    {
        return true;
    }

    public function gc($maxlifetime = 0)
    {
        return Z::db($this->dbConfig)->delete($this->dbTable, ['timestamp <' => time()])->execute() > 0;
    }

    public function open($path, $name)
    {
        return true;
    }

    public function close()
    {
        return true;
    }
}
