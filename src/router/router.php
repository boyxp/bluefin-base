<?php
namespace router;
interface router
{
	public function add(string $pattern, $handle):router;
	public function remove(string $pattern):router;
	public function flush():router;

	public function route($subject):bool;
	public function getHandle();
	public function getMatches():array;
}
