<?php

namespace legionpe\branch;

class Credentials{
	public $mysqli_host;
	public $mysqli_user;
	public $mysqli_pass;
	public $mysqli_schema;
	public $mysqli_port = 22;
	public function __construct($mysqli_host, $mysqli_user, $mysqli_pass, $mysqli_schema, $mysqli_port = 22){
		$this->mysqli_host = $mysqli_host;
		$this->mysqli_user = $mysqli_user;
		$this->mysqli_pass = $mysqli_pass;
		$this->mysqli_schema = $mysqli_schema;
		$this->mysqli_port = $mysqli_port;
	}
	public function getMySQLi(){
		return new \mysqli($this->mysqli_host, $this->mysqli_user, $this->mysqli_pass, $this->mysqli_schema, $this->mysqli_port);
	}
}
