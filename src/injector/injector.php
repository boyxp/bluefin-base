<?php
namespace bluefin\base\injector;
use bluefin\base\locator\locator;
interface injector
{
	public static function inject(locator $locator):bool;
}
