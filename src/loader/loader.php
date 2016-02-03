<?php
namespace loader;
interface loader
{
	public function add(string $dir, bool $prepend=false):bool;
	public function load(string $class):bool;
	public function register(bool $prepend=false):bool;
	public function unregister():bool;
}
