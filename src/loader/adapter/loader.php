<?php
declare(strict_types=1);
namespace bluefin\component\loader\adapter;
use bluefin\component\loader\loader as loaderInterface;
class loader implements loaderInterface
{
	private $_paths      = [];
	private $_registered = false;

	public function __construct(array $paths=[])
	{
		$paths = array_merge($paths, explode(PATH_SEPARATOR, get_include_path()));
		foreach($paths as $path) {
			if(is_dir($path)) {
				$this->_paths[] = $path;
			}
		}
	}

	public function add(string $dir):bool
	{
		if(!in_array($dir, $this->_paths) and is_dir($dir)) {
			$this->_paths[] = $dir;
			return true;
		} else {
			return false;
		}
	}

	public function load(string $class):bool
	{
		$file = DIRECTORY_SEPARATOR.strtr($class, ['\\'=>DIRECTORY_SEPARATOR, '_'=>DIRECTORY_SEPARATOR]).'.php';

		foreach($this->_paths as $dir) {
			if(is_file($dir.$file)) {
				include($dir.$file);
				return true;
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
