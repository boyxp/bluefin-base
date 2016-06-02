<?php
declare(strict_types=1);
namespace bluefin\base\loader\adapter;
use bluefin\base\loader\loader as loaderInterface;
class prefix implements loaderInterface
{
	private $_registered = false;
	private $_prefixDirs = [];

	public function add(string $prefix, string $dir):bool
	{
		if(!is_dir($dir)) {
			return false;
		}

		$prefix = rtrim($prefix, '\\');

		if(!isset($this->_prefixDirs[$prefix{0}][$prefix])) {
			$this->_prefixDirs[$prefix{0}][$prefix][$dir] = strlen($prefix);
			return true;

		} elseif(!in_array($dir, $this->_prefixDirs[$prefix{0}][$prefix])) {
			$this->_prefixDirs[$prefix{0}][$prefix][$dir] = strlen($prefix);
			return true;

		} else {
			return false;
		}
	}

	public function load(string $class):bool
	{
		if(!isset($this->_prefixDirs[$class{0}])) {
			return false;
		}

		$path = strtr($class, '\\', DIRECTORY_SEPARATOR).'.php';

		foreach($this->_prefixDirs[$class{0}] as $prefix => $dirs) {
			if(strpos($class, $prefix)===0) {
				foreach($dirs as $dir => $length) {
					if(is_file($file=$dir.DIRECTORY_SEPARATOR.substr($path, $length))) {
						include($file);
						return true;
					}
				}
			}
		}

		return false;
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
