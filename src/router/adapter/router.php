<?php
declare(strict_types=1);
namespace bluefin\component\router\adapter;
use bluefin\component\router\router as routerInterface;
use bluefin\component\registry\registry;
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
			$key   = "MATCH:{$nodes[0]}";
			unset($nodes[0]);

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
			$rules = $this->_registry->get("MATCH:{$nodes[0]}");
			if($rules) {
				$last    = &$rules;
				$matches = [];
				for($i=1,$count=count($nodes),$end=$count-1;$i<$count;$i++) {
					if(isset($last[$nodes[$i]])) {
						$last = &$last[$nodes[$i]];
					} elseif(isset($last['*']) and ctype_alnum($nodes[$i])) {
						$matches[] = $nodes[$i];
						$last      = &$last['*'];
					} else {
						return false;
					}

					if($i===$end and isset($last['#handle'])) {
						$this->_handle = $last['#handle'];
						$this->_matches= $matches;
						return true;
					}
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
