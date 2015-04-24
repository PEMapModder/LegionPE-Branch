<?php

namespace legionpe\branch\utils;

use legionpe\branch\BranchPlugin;
use pocketmine\scheduler\AsyncTask;

abstract class QueryAsyncTask extends AsyncTask{
	private $resultSuccess = false;
	public function onRun(){
		$mainClass = BranchPlugin::currentImpl();
		try{
			/** @noinspection PhpUndefinedMethodInspection */
			$mysql = new \mysqli($mainClass::getMysqlHost(), $mainClass::getMysqlUser(), $mainClass::getMysqlPass(), $mainClass::getMysqlSchema(), $mainClass::getMysqlPort());
		}catch(\RuntimeException $e){
			return;
		}

		$this->resultSuccess = $this->execute($mysql);
	}
	protected abstract function execute(\mysqli $mysqli);
}
