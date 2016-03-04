<?php
declare(strict_types=1);
namespace bluefin\component\injector\adapter;
use bluefin\component\locator\locator;
use bluefin\component\injector\injector as injectorInterface;
class injector implements injectorInterface
{
	protected static $_locator;

	public static function inject(locator $locator):bool
	{
		static::$_locator = $locator;
		return true;
	}
}
