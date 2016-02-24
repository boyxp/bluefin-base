<?php
namespace locator;
interface locator
{
	public function get(string $service);
	public function __get(string $service);

	public function set(string $service, $instance):bool;
	public function __set(string $service, $instance):bool;

	public function make(string $service, array $args=null);
	public function bind(string $service, array $args):closure;
	public function alias(string $alias, string $service):bool;

	public function has(string $service):bool;
	public function __isset(string $service):bool;

	public function remove(string $service):bool;
	public function __unset(string $service):bool;
}
