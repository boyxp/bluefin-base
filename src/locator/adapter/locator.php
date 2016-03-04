<?php
declare(strict_types=1);
namespace bluefin\component\locator\adapter;
use closure;
use ReflectionClass;
use bluefin\component\locator\exception;
use bluefin\component\locator\locator as locatorInterface;
class locator implements locatorInterface
{
	private $_instances = [];
	private $_closures  = [];
	private $_aliases   = [];
	private $_bindings  = [];
	private $_namespace = '';

	public function __construct($namespace='')
	{
		$this->_namespace = $namespace;
	}

	public function get(string $service)
	{
		if(isset($this->_aliases[$service])) {
			$service = $this->_aliases[$service];
		}

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

		$class = $this->_namespace.str_replace('_', '\\adapter\\', $service);
		if(class_exists($class)) {
			if(is_null($args)) {
				if(!isset($this->_bindings[$service])) {
					return new $class;
				} else {
					$args = $this->_bindings[$service];
				}
			}

			return (new ReflectionClass($class))->newInstanceArgs($args);
		} else {
			throw new exception('error');
		}
	}

	public function bind(string $service, array $args):closure
	{
		$this->_bindings[$service] = $args;

		return function() use($service,$args) {
			return $this->make($service, $args);
		};
	}

	public function alias(string $alias, string $service):bool
	{
		if(!isset($this->_aliases[$alias]) and $this->has($service)) {
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

		} elseif(strpos($service, '_')===false and interface_exists($this->_namespace.$service.'\\'.$service)) {
			return true;

		} elseif(strpos($service, '_')!==false and class_exists($this->_namespace.str_replace('_', '\\adapter\\', $service))) {
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
