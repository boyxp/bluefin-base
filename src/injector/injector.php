<?php
declare(strict_types=1);
namespace bluefin\base\injector;
use bluefin\base\locator\locator;
interface injector
{
	public static function inject(locator $locator):bool;
}
