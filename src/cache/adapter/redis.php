<?php
declare(strict_types=1);
namespace bluefin\component\cache\adapter;
use bluefin\component\cache\cache as cacheInterface;
use bluefin\orm\connection\adapter\redis;
class redis implements cacheInterface
{
	private $_redis  = null;
	private $_prefix = null;

	public function __construct(connection $redis=null, $prefix=null)
	{
		$this->_redis = $redis;
		if($prefix) {
			$this->_prefix = "{$prefix}:";
		} else {
			$this->_prefix = "CACHE:{$_SERVER['SERVER_NAME']}:";
		}
	}

	public function get(string $key)
	{
		return $this->_redis->get($this->_prefix.$key);
	}

	public function __get(string $key)
	{
		return $this->get($this->_prefix.$key);
	}

	public function set(string $key, $value, int $ttl=0):bool
	{
		$this->_redis->set($this->_prefix.$key, $value);
		if($ttl>0) {
			$this->expire($this->_prefix.$key, $ttl);
		}

		return $this;
	}

	public function __set(string $key, $value):bool
	{
		return $this->set($this->_prefix.$key, $value);
	}

	public function exists(string $key):bool
	{
		return $this->_redis->exists($this->_prefix.$key);
	}

	public function __isset(string $key):bool
	{
		return $this->exists($key);
	}

	public function remove(string $key):bool
	{
		$this->_redis->del($this->_prefix.$key);
		return $this;
	}

	public function __unset(string $key):bool
	{
		return $this->delete($key);
	}

	public function expire(string $key, int $ttl=60):bool
	{
		$this->_redis->expire($this->_prefix.$key, intval($ttl));
		return $this;
	}

	public function ttl(string $key):int
	{
		$ttl = $this->_redis->ttl($this->_prefix.$key);
		return $ttl>0 ? $ttl : 0;
	}

	public function flush():bool
	{
	}
}
