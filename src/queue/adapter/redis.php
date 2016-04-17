<?php
declare(strict_types=1);
namespace bluefin\component\queue\adapter;
use bluefin\orm\connection\adapter\redis as connection;
use bluefin\component\queue\queue as queueInterface;
class redis implements queueInterface
{
	private $_redis = null;
	private $_key   = null;

	public function __construct(connection $redis=null, $key=null)
	{
		$this->_redis = $redis;
		if($key) {
			$this->_key = "QUEUE:{$key}";
		} else {
			$this->_key = "QUEUE:{$_SERVER['SERVER_NAME']}";
		}
	}

	public function enqueue(string $message):bool
	{
		$this->_redis->lpush($this->_key, $message);
		return true;
	}

	public function dequeue():string
	{
		$result = $this->_redis->rpop($this->_key);
		return is_null($result) ? '' : $result;
	}

	public function purge():bool
	{
		$result = $this->_redis->del($this->_key);
		return $result===1;
	}

	public function delete():bool
	{
		$result = $this->_redis->del($this->_key);
		return $result===1;
	}
}
