<?php
namespace bluefin\component\injector;
use bluefin\component\locator\locator;
interface injector
{
	public static function inject(locator $locator):bool;
}
