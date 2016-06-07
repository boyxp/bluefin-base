<?php
declare(strict_types=1);
namespace bluefin\base\config\adapter;
use bluefin\base\registry\adapter\apcu;
class config extends apcu
{
	public function __construct(string $prefix='config')
	{
		parent::__construct($prefix);
	}
}
