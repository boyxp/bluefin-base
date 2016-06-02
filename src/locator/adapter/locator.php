<?php
declare(strict_types=1);
namespace bluefin\base\locator\adapter;
use closure;
use ReflectionClass;
use bluefin\base\locator\exception;
use bluefin\base\locator\locator as locatorInterface;
class locator implements locatorInterface
{
	private $_instances  = [];
	private $_closures   = [];
	private $_aliases    = [];
	private $_bindings   = [];
	private $_namespaces = [];
	private $_classmap   = [];

	public function __construct(array $namespaces=[])
	{
		$this->_namespaces = $namespaces;
	}

	public function add(string $namespace):bool
	{
		$namespace = rtrim($namespace, '\\');

		if(in_array($namespace, $this->_namespaces)) {
			return false;
		} else {
			$this->_namespaces[] = $namespace;
			return true;
		}
	}

	public function get(string $service)
	{
		if(isset($this->_aliases[$service])) {
			$args    = isset($this->_bindings[$service]) ? $this->_bindings[$service] : null;
			$service = $this->_aliases[$service];
		} else {
			$args = null;
		}

		if(isset($this->_instances[$service])) {

		} elseif(isset($this->_closures[$service])) {
			$closure = $this->_closures[$service];
			$this->_instances[$service] = $closure();

		} elseif($this->has($service)) {
			$this->_instances[$service] = $this->make($service, $args);

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
		$class = $this->_lookup($service);
		if(!is_null($class)) {
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

		} elseif($this->_lookup($service)) {
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
		unset($this->_closures[$service], $this->_instances[$service], $this->_aliases[$service], $this->_bindings[$service]);
		return true;
	}

	public function __unset(string $service):bool
	{
		return $this->remove($service);
	}

	private function _lookup(string $service)
	{
		if(isset($this->_classmap[$service])) {
			return $this->_classmap[$service];
		}

		if(strpos($service, '_')===false) {
			$component = $service;
			$adapter   = $service;
		} else {
			list($component, $adapter) = explode('_', $service);
		}

		foreach($this->_namespaces as $namespace) {
			$class = sprintf($namespace, $component).'\\'.$adapter;
			if(class_exists($class)) {
				$this->_classmap[$service] = $class;
				return $class;
			}
		}

		return null;
	}
}
