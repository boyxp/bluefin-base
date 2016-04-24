<?php
declare(strict_types=1);
namespace bluefin\component\router\adapter;
use bluefin\component\registry\registry;
use bluefin\component\router\router as routerInterface;
class router implements routerInterface
{
	private $_registry = null;
	private $_handle   = null;
	private $_matches  = [];

	public function __construct(registry $registry)
	{
		$this->_registry = $registry;
	}

	public function add(string $pattern, $handle):routerInterface
	{
		$pattern = preg_replace('/\{[a-z0-9]+\}/i', '*', $pattern);

		if(strpos($pattern, '*')===false) {
			$this->_registry->set("STATIC:{$pattern}", $handle);
		} else {
			$nodes = explode('/', ltrim($pattern, '/'));
			$key   = 'MATCH:'.next($nodes).':'.count($nodes);
			$tree  = $this->_registry->exists($key) ? $this->_registry->get($key) : [];
			$curr  = &$tree;
			foreach($nodes as $node) {
				if(!isset($curr[$node])) {
					$curr[$node] = [];
				}

				$curr = &$curr[$node];
			}
			$curr['#handle'] = $handle;

			$this->_registry->set($key, $tree);
		}

		return $this;
	}

	public function remove(string $pattern):routerInterface
	{
		return $this;
	}

	public function flush():routerInterface
	{
		$this->_registry->flush();
		return $this;
	}

	public function route(string $subject):bool
	{
		$this->_handle  = null;
		$this->_matches = [];

		if($handle=$this->_registry->get("STATIC:{$subject}")) {
			$this->_handle = $handle;
			return true;
		} else {
			$nodes = explode('/', ltrim($subject, '/'));
			$key   = 'MATCH:'.next($nodes).':'.count($nodes);
			$rules = $this->_registry->get($key);
			if(!$rules) {
				$key   = 'MATCH:*:'.count($nodes);
				$rules = $this->_registry->get($key);
			}

			if($rules) {
				$last    = &$rules;
				$matches = [];
				for($i=0,$count=count($nodes);$i<$count;$i++) {
					if(isset($last[$nodes[$i]])) {
						$last = &$last[$nodes[$i]];
					} elseif(isset($last['*']) and ctype_alnum(strtr($nodes[$i], array(':'=>'', '_'=>'')))) {
						$matches[] = $nodes[$i];
						$last      = &$last['*'];
					} else {
						return false;
					}
				}

				if(isset($last['#handle'])) {
					$this->_handle = $last['#handle'];
					$this->_matches= $matches;
					return true;
				}
			}
		}

		return false;
	}

	public function getHandle()
	{
		return $this->_handle;
	}

	public function getMatches():array
	{
		return $this->_matches;
	}
}
