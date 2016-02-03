<?php
declare(strict_types=1);
namespace loader\adapter;
use loader\loader as loaderInterface;
class psr implements loaderInterface
{
	private static $_registered = false;
	private static $_includeDir = array();

	public function __construct()
	{
		static::$_includeDir = explode(PATH_SEPARATOR, get_include_path());
	}

	public function add(string $dir, bool $prepend=false):bool
	{
		if(in_array($dir, static::$_includeDir)===false) {
			if($prepend) {
				array_unshift(static::$_includeDir, $dir);
			} else {
				static::$_includeDir[] = $dir;
			}

			return true;
		}

		return false;
	}

	public function load(string $class):bool
	{
		$file = DIRECTORY_SEPARATOR.strtr($class, '\\', DIRECTORY_SEPARATOR).'.php';

		foreach(static::$_includeDir as $dir) {
			if(is_file($dir.$file)) {
				include($dir.$file);
				return true;
			}
		}

		return false;
	}

	public function register(bool $prepend=false):bool
	{
		if(!static::$_registered) {
			spl_autoload_register(array($this, 'load'), true, $prepend);
			static::$_registered = true;

			return true;
		}

		return false;
	}

	public function unregister():bool
	{
		if(static::$_registered===false) {
			return false;
		}

		return spl_autoload_unregister(array($this, 'load'));
	}
}
