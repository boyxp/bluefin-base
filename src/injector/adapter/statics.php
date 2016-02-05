<?php
declare(strict_types=1);
namespace injector\adapter;
use injector\injector;
use locator\locator;
class statics implements injector
{
	protected static $_locator;

	public static function inject(locator $locator):bool
	{
		static::$_locator = $locator;
		return true;
	}
}
