<?php
declare(strict_types=1);
namespace injector\adapter;
use locator\locator;
use injector\injector as injectorInterface;
class injector implements injectorInterface
{
	protected static $_locator;

	public static function inject(locator $locator):bool
	{
		static::$_locator = $locator;
		return true;
	}
}
