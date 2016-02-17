<?php
namespace cache;
interface cache
{
	public function get(string $key);
	public function __get(string $key);

	public function set(string $key, $value, int $ttl=0):bool;
	public function __set(string $key, $value):bool;

	public function expire(string $key, int $ttl):bool;
	public function ttl(string $key):int;
	public function exists(string $key):bool;
	public function delete(string $key):bool;
	public function flush():bool;
}
