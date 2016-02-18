<?php
namespace router;
interface router
{
	public function add(string $pattern, $handle):bool;
	public function remove(string $pattern):bool;
	public function flush():bool;

	public function route($subject):bool;
	public function getHandle();
	public function getMatches():array;
}
