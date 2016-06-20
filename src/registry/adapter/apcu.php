<?php
declare(strict_types=1);
namespace bluefin\base\registry\adapter;
use bluefin\base\registry\registry as registryInterface;
class apcu implements registryInterface
{
	private $_prefix = '';
	private $_cache  = [];

	public function __construct(string $prefix=null)
	{
		if(!is_null($prefix)) {
			$this->_prefix = $prefix.':';

		} elseif(isset($_SERVER['HTTP_HOST'])) {
			$this->_prefix = "APCU:{$_SERVER['HTTP_HOST']}:";

		} else {
			$this->_prefix = 'APCU:';
		}
	}

	public function get(string $key)
	{
		return \apcu_fetch($this->_prefix.$key);
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
		\apcu_store($this->_prefix.$key, $value);
		return $this;
	}

	public function __set(string $key, $value):registryInterface
	{
		$this->_cache[$key] = $value;
		return $this->set($key, $value);
	}

	public function exists(string $key):bool
	{
		return \apcu_exists($this->_prefix.$key); 
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

		return \apcu_delete($this->_prefix.$key);
	}

	public function __unset(string $key):bool
	{
		return $this->delete($key);
	}

	public function flush():registryInterface
	{
		$this->_cache = [];

		$iterator = class_exists('\APCUIterator') ? new \APCUIterator() : new \APCIterator('user');
		foreach($iterator as $key=>$value) {
			if($this->_prefix==='' or strpos($key, $this->_prefix)===0) {
				\apcu_delete($key);
			}
		}
		$iterator = null;
		return $this;
	}
}
