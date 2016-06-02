<?php
declare(strict_types=1);
namespace bluefin\base\injector\adapter;
use bluefin\base\locator\locator;
use bluefin\base\injector\injector as injectorInterface;
class injector implements injectorInterface
{
	protected static $_locator;

	public static function inject(locator $locator):bool
	{
		static::$_locator = $locator;
		return true;
	}
}
