<?php
declare(strict_types=1);
namespace bluefin\base\listener\adapter;
use closure;
use bluefin\base\listener\listener as listenerInterface;
class listener extends \injector implements listenerInterface
{
	private $_instance = null;
	private $_events   = [];

	public function __construct(string $service)
	{
		$this->_instance = static::$locator->$service;
	}

	public function before(string $method, closure $callback):bool
	{
		$this->_events[$method]['before'][] = $callback;
		return true;
	}

	public function after(string $method, closure $callback):bool
	{
		$this->_events[$method]['after'][] = $callback;
		return true;
	}

	public function __call(string $method, array $args)
	{
		if(isset($this->_events[$method]['before'])) {
			foreach($this->_events[$method]['before'] as $callback) {
				call_user_func($callback, $args, $this->_instance);
			}
		}

		$result = call_user_func_array([$this->_instance, $method], $args);

		if(isset($this->_events[$method]['after'])) {
			foreach($this->_events[$method]['after'] as $callback) {
				call_user_func($callback, $result, $this->_instance);
			}
		}

		return $result;
	}
}
