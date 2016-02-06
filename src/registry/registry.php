<?php
namespace registry;
interface registry
{
	public function get(string $key);
	public function __get(string $key);
	public function set(string $key, $value, int $ttl=0);
	public function __set(string $key, $value);
	public function exists(string $key):bool;
	public function delete(string $key):bool;
	public function flush():bool;
}
