<?php
namespace bluefin\component\queue;
interface queue
{
	public function enqueue($message):bool;
	public function dequeue();
	public function purge():bool;
	public function delete():bool;
}
