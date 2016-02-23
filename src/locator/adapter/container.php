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
			return null;
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
		//if(class_exists($class)) {}
	}

	public function bind(string $service, array $args):closure
	{
		return function() use($service,$args) {
			return static::make($service, $args);
		};
	}

	public function alias(string $alias, string $service):bool
	{
	}

	public function has(string $service):bool
	{
	}

	public function remove(string $service):bool
	{
	}
}
