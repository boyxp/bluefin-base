<?php
declare(strict_types=1);
namespace registry\adapter;
use registry\registry;
class apcu implements registry
{
	private $_prefix = '';
	private $_cache  = array();

	public function __construct($prefix=null)
	{
		if(isset($_SERVER['HTTP_HOST'])) {
			$this->_prefix .= $_SERVER['HTTP_HOST'].':';
		}

		if($prefix!==null and is_string($prefix)) {
			$this->_prefix .= $prefix.':';
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

	public function set(string $key, $value):registry
	{
		\apcu_store($this->_prefix.$key, $value);
		return $this;
	}

	public function __set(string $key, $value):registry
	{
		$this->_cache[$key] = $value;
		return $this->set($key, $value);
	}

	public function exists(string $key):bool
	{
		return \apcu_exists($this->_prefix.$key); 
	}

	public function delete(string $key):bool
	{
		if(isset($this->_cache[$key])) {
			unset($this->_cache[$key]);
		}

		return \apcu_delete($this->_prefix.$key);
	}

	public function flush():registry
	{
		$this->_cache = array();

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
