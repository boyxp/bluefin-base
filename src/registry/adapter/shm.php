<?php
declare(strict_types=1);
namespace bluefin\base\registry\adapter;
use bluefin\base\registry\registry as registryInterface;
class shm implements registryInterface
{
	private $_cache = [];
	private $_shmid = null;

	public function __construct(string $namespace=null)
	{
		if(is_null($namespace)) {
			$namespace = $_SERVER['HTTP_HOST'];
		}

		$this->_shmid = shm_attach(crc32($namespace));
	}

	public function get(string $key)
	{
		$key = crc32($key);
		if(shm_has_var($this->_shmid, $key)) {
			return unserialize(shm_get_var($this->_shmid, $key));
		} else {
			return null;
		}
	}

	public function __get(string $key)
	{
		if(!isset($this->_cache[$key])) {
			$this->_cache[$key] = $this->get($key);
		}

		return $this->_cache[$key];
	}

	public function set(string $key, $value):registryInterface
	{
		$key   = crc32($key);
		$value = serialize($value);
		shm_put_var($this->_shmid, $key, $value);
		return $this;
	}

	public function __set(string $key, $value):registryInterface
	{
		$this->_cache[$key] = $value;
		return $this->set($key, $value);
	}

	public function exists(string $key):bool
	{
		$key = crc32($key);
		return shm_has_var($this->_shmid, $key);
	}

	public function __isset(string $key):bool
	{
		return $this->exists($key);
	}

	public function remove(string $key):bool
	{
		if(isset($this->_cache[$key])) {
			unset($this->_cache[$key]);
		}

		$key = crc32($key);
		if(shm_has_var($this->_shmid, $key)) {
			return shm_remove_var($this->_shmid, $key);
		} else {
			return false;
		}
	}

	public function __unset(string $key):bool
	{
		return $this->delete($key);
	}

	public function flush():registryInterface
	{
		$this->_cache = [];
		shm_remove($this->_shmid);
		return $this;
	}

	public function __destruct()
	{
		shm_detach($this->_shmid);
	}
}
