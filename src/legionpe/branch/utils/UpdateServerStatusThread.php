<?php

namespace legionpe\branch\utils;

class UpdateServerStatusThread extends QueryAsyncTask{
	/** @var int */
	private $sid;
	/** @var int */
	private $cnt;
	/** @var int */
	private $dataCreation;
	public function __construct($sid, $cnt){
		$this->sid = $sid;
		$this->cnt = $cnt;
		$this->dataCreation = time();
	}
	protected function execute(\mysqli $mysqli){
		return $mysqli->query("UPDATE active_servers SET last_up=from_unixtime($this->dataCreation),cnt=$this->cnt WHERE sid=$this->sid");
	}
}
