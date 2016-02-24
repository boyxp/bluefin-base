<?php
declare(strict_types=1);
namespace locator\adapter;
use locator\locator;
class container implements locator
{
	private $_instances = [];
	private $_closures  = [];
	private $_aliases   = [];

	public function get(string $service)
	{
		if(isset($this->_closures[$service])) {
			$closure = $this->_closures[$service];
			$this->_instances[$service] = $closure();
			return $this->_instances[$service];

		} elseif(isset($this->_instances[$service])) {
			return $this->_instances[$service];

		} else {
			throw new exception('error');
		}
	}

	public function __get(string $service)
	{
		return $this->get($service);
	}

	public function set(string $service, $instance):bool
	{
		if(gettype($instance)!=='object') {
			return false;

		} elseif(get_class($instance)==='Closure') {
			$this->_closures[$service] = $instance;

		} else {
			$this->_instances[$service] = $instance;
		}

		return true;
	}

	public function __set(string $service, $instance):bool
	{
		return $this->set($service, $instance);
	}

	public function make(string $service, array $args=null)
	{
		if(strpos($service, '_')===false) {
			$service .= '_'.$service;
		}

		$class = str_replace('_', '\\adapter\\', $service);
		if(class_exists($class)) {
			$reflection = new ReflectionClass($class);
			$instance   = $reflection->newInstanceArgs($args);
			return $instance;
		} else {
			throw new exception('error');
		}
	}

	public function bind(string $service, array $args):closure
	{
		return function() use($service,$args) {
			return static::make($service, $args);
		};
	}

	public function alias(string $alias, string $service):bool
	{
		$this->_aliases[$alias] = $service;
		return true;
	}

	public function has(string $service):bool
	{
		return isset($this->_closures[$service]) or isset($this->_instances[$service]);
	}

	public function __isset(string $service):bool
	{
		return $this->has($service);
	}

	public function remove(string $service):bool
	{
		unset($this->_closures[$service], $this->_instances[$service]);
		return true;
	}

	public function __unset(string $service):bool
	{
		return $this->remove($service);
	}
}
