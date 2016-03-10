<?php
namespace bluefin\component\router;
interface router
{
	public function add(string $pattern, $handle):router;
	public function remove(string $pattern):router;
	public function flush():router;

	public function route(string $subject):bool;
	public function getHandle();
	public function getMatches():array;
}
