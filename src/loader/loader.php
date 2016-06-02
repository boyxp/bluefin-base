<?php
namespace bluefin\base\loader;
interface loader
{
	public function load(string $class):bool;
	public function register(bool $prepend=false):bool;
	public function unregister():bool;
}
