<?php
namespace bluefin\component\queue;
interface queue
{
	public function enqueue(string $message):bool;
	public function dequeue():string;
	public function purge():bool;
	public function delete():bool;
}
