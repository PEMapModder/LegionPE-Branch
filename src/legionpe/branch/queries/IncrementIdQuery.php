<?php

namespace legionpe\branch\queries;

class IncrementIdQuery extends AsyncQuery{
	const USER = "uid";
	const TEAM = "tid";
	/** @var string */
	private $name;
	public function __construct($name){
		$this->name = $name;
	}
	protected function onPreQuery(){
		$mysql = $this->getConn();
		$mysql->query("LOCK TABLES ids WRITE");
	}
	public function getQuery(){
		return "SELECT value FROM ids WHERE name='$this->name'";
	}
	protected function onPostQuery(){
		$mysql = $this->getConn();
		$mysql->query("UPDATE ids SET value=value+1 WHERE name='$this->name'");
		$mysql->query("UNLOCK TABLES");
	}
	public function getResultType(){
		return self::TYPE_ASSOC;
	}
}
