<?php
declare(strict_types=1);
namespace bluefin\base\listener;
use closure;
interface listener
{
	public function before(string $method, closure $callback):bool;
	public function after(string $method, closure $callback):bool;
	public function __call(string $method, array $args);
}
