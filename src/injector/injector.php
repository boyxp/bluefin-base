<?php
namespace injector;
use locator\locator;
interface injector
{
	public static function inject(locator $locator):bool;
}
