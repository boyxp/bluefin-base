<?php
declare(strict_types=1);
namespace locator\adapter;
use closure;
use ReflectionClass;
use locator\exception;
use locator\locator as locatorInterface;
class locator implements locatorInterface
{
	private $_instances = [];
	private $_closures  = [];
	private $_aliases   = [];

	public function get(string $service)
	{
		$service = isset($this->_aliases[$service]) ? $this->_aliases[$service] : $service;

		if(isset($this->_instances[$service])) {

		} elseif(isset($this->_closures[$service])) {
			$closure = $this->_closures[$service];
			$this->_instances[$service] = $closure();

		} elseif($this->has($service)) {
			$this->_instances[$service] = $this->make($service);

		} else {
			throw new exception('error');
		}

		return $this->_instances[$service];
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
			if(is_null($args)) {
				$instance   = new $class;
			} else {
				$reflection = new ReflectionClass($class);
				$instance   = $reflection->newInstanceArgs($args);
			}
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
		if($this->has($service)) {
			$this->_aliases[$alias] = $service;
			return true;

		} else {
			return false;
		}
	}

	public function has(string $service):bool
	{
		if(isset($this->_instances[$service])) {
			return true;

		} elseif(isset($this->_closures[$service])) {
			return true;

		} elseif(isset($this->_aliases[$service])) {
			return true;

		} elseif(strpos($service, '_')===false and interface_exists($service.'\\'.$service)) {
			return true;

		} elseif(strpos($service, '_')!==false and class_exists(str_replace('_', '\\adapter\\', $service))) {
			return true;

		} else {
			return false;
		}
	}

	public function __isset(string $service):bool
	{
		return $this->has($service);
	}

	public function remove(string $service):bool
	{
		unset($this->_closures[$service], $this->_instances[$service], $this->_aliases[$service]);
		return true;
	}

	public function __unset(string $service):bool
	{
		return $this->remove($service);
	}
}
