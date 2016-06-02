<?php
namespace bluefin\base\registry;
interface registry
{
	public function get(string $key);
	public function __get(string $key);
	public function set(string $key, $value):registry;
	public function __set(string $key, $value):registry;
	public function exists(string $key):bool;
	public function __isset(string $key):bool;
	public function remove(string $key):bool;
	public function __unset(string $key):bool;
	public function flush():registry;
}
