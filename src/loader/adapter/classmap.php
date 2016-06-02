<?php
declare(strict_types=1);
namespace bluefin\base\loader\adapter;
use bluefin\base\loader\loader as loaderInterface;
class classmap implements loaderInterface
{
	private $_classmap   = [];
	private $_registered = false;

	public function __construct(array $classmap=[])
	{
		$this->_classmap = $classmap;
	}

	public function add(string $class, string $file):bool
	{
		if(!isset($this->_classmap[$class]) and is_file($file)) {
			$this->_classmap[$class] = $file;
			return true;
		} else {
			return false;
		}
	}

	public function load(string $class):bool
	{
		if(isset($this->_classmap[$class]) and is_file($this->_classmap[$class])) {
			include($this->_classmap[$class]);
			return true;
		} else {
			return false;
		}
	}

	public function register(bool $prepend=false):bool
	{
		if(!$this->_registered) {
			$this->_registered = spl_autoload_register([$this, 'load'], true, $prepend);
			return $this->_registered;
		} else {
			return false;
		}
	}

	public function unregister():bool
	{
		if($this->_registered===false) {
			return false;
		} else {
			return spl_autoload_unregister([$this, 'load']);
		}
	}
}
