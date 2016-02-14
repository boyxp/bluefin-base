<?php
namespace dispatcher;
interface dispatcher
{
	public function dispatch($handle, array $params=array()):bool;
	public function abort():bool;
	public function forward($handle, array $params=array()):bool;
}
