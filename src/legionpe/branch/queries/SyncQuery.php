<?php

namespace legionpe\branch\queries;

use legionpe\branch\BranchPlugin;

class SyncQuery{
	const TYPE_RAW = 0;
	const TYPE_ASSOC = 1;
	const TYPE_ALL = 2;
	const COL_STRING = 0;
	const COL_INT = 1;
	const COL_FLOAT = 2;
//	const COL_BOOL = 3;
//	const COL_TIMESTAMP = 4;
	private static $defaultValues = [
		self::COL_STRING => "",
		self::COL_INT => 0,
		self::COL_FLOAT => 0.0,
//		self::COL_BOOL => false,
//		self::COL_TIMESTAMP => "1969-12-31 19:00:00",
	];
	/** @var BranchPlugin */
	private $main;
	/** @var string */
	private $query;
	/** @var int */
	private $resultType;
	/** @var int[] */
	private $expectedColumns = [];
	public $result;
	public function __construct(BranchPlugin $main, $query, $resultType, $expectedColumns = []){
		$this->main = $main;
		$this->query = $query;
		$this->resultType = $resultType;
		$this->expectedColumns = $expectedColumns;
	}
	public function exe(){
		$mysql = $this->getConn();
		$result = $mysql->query($query = $this->query);
		if($result === false){
			$this->setResult(["success" => false, "query" => $query, "error" => $mysql->error]);
			return;
		}
		$type = $this->resultType;
		if($result instanceof \mysqli_result){
			if($type === self::TYPE_ASSOC){
				$row = $result->fetch_assoc();
				$result->close();
				$this->processRow($row);
				$this->setResult(["success" => true, "query" => $query, "result" => $row, "resulttype" => self::TYPE_ASSOC]);
			}elseif($type === self::TYPE_ALL){
				$set = [];
				while(is_array($row = $result->fetch_assoc())){
					$this->processRow($row);
					$set[] = $row;
				}
				$result->close();
				$this->setResult(["success" => true, "query" => $query, "result" => $row, "resulttype" => self::TYPE_ALL]);
			}
		}
		$this->setResult(["success" => true, "query" => $query, "resulttype" => self::TYPE_RAW]);
	}
	/**
	 * @return \mysqli
	 */
	public function getConn(){
		return $this->main->getMysql();
	}
	private function processRow(&$r){
		if(!is_array($r)){
			return;
		}
		foreach($this->expectedColumns as $column => $col){
			if(!isset($r[$column])){
				$r[$column] = self::$defaultValues[$col];
			}elseif($col === self::COL_INT){
				$r[$column] = (int) $r[$column];
			}elseif($col === self::COL_FLOAT){
				$r[$column] = (float) $r[$column];
			}
		}
	}
	public function esc(\mysqli $mysql, $str){
		return is_string($str) ? "'{$mysql->escape_string($str)}'" : (string) $str;
	}
	private function setResult($array){
		$this->result = $array;
	}
}
