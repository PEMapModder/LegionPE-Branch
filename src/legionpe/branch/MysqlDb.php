<?php

namespace legionpe\branch;

class MysqlDb{
	private $mysqli;
	public function __construct(Credentials $credentials){
		$this->mysqli = $credentials->getMySQLi();
	}
}
