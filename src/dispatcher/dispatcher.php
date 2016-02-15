<?php
namespace dispatcher;
interface dispatcher
{
	public function dispatch($handle, array $params=[]):bool;
	public function abort():bool;
	public function forward($handle, array $params=[]):bool;
}
