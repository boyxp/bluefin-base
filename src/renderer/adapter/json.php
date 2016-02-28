<?php
declare(strict_types=1);
namespace renderer\adapter;
use renderer\renderer as rendererInterface;
class json implements rendererInterface
{
	private $_charset = null;

	public function __construct($charset='utf-8')
	{
		$this->_charset = $charset;
	}

	public function render($content):bool
	{
		header("Content-type: application/json;charset={$this->_charset}");
		echo json_encode($content);
		return true;
	}
}
