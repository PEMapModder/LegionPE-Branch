<?php

namespace legionpe\branch\queries;

use legionpe\branch\BranchPlugin;
use pocketmine\scheduler\AsyncTask;

abstract class AsyncQuery extends AsyncTask{
	const KEY_MYSQL = "legionpe.branch.query.mysql";
	const TYPE_RAW = 0;
	const TYPE_ASSOC = 1;
	const TYPE_ALL = 2;
	const COL_STRING = 0;
	const COL_INT = 1;
	const COL_UNIXTIME = 1;
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
	public function onRun(){
		$mysql = $this->getConn();
		$this->onPreQuery();
		$result = $mysql->query($query = $this->getQuery());
		$this->onPostQuery();
		if($result === false){
			$this->setResult(["success" => false, "query" => $query, "error" => $mysql->error]);
			return;
		}
		$type = $this->getResultType();
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
			return;
		}
		$this->setResult(["success" => true, "query" => $query, "resulttype" => self::TYPE_RAW]);
	}
	/**
	 * @return \mysqli
	 */
	public function getConn(){
		$mysql = $this->getFromThreadStore(self::KEY_MYSQL);
		if(!($mysql instanceof \mysqli)){
			/** @var BranchPlugin $class */
			$class = BranchPlugin::currentImpl();
			$mysql = new \mysqli($class::getMysqlHost(), $class::getMysqlUser(), $class::getMysqlPass(), $class::getMysqlSchema(), $class::getMysqlPort());
			$this->saveToThreadStore(self::KEY_MYSQL, $mysql);
		}
		return $mysql;
	}
	private function processRow(&$r){
		if(!is_array($r)){
			return;
		}
		foreach($this->getExpectedColumns() as $column => $col){
			if(!isset($r[$column])){
				$r[$column] = self::$defaultValues[$col];
			}elseif($col === self::COL_INT){
				$r[$column] = (int) $r[$column];
			}elseif($col === self::COL_FLOAT){
				$r[$column] = (float) $r[$column];
			}
		}
	}
	protected function onPreQuery(){}
	public abstract function getQuery();
	protected function onPostQuery(){}
	public abstract function getResultType();
	public function getExpectedColumns(){
		return [];
	}
	public function esc($str){
		return is_string($str) ? "'{$this->getConn()->escape_string($str)}'" : (string) $str;
	}
}
