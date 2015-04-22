<?php

namespace legionpe\branch\queries;

use legionpe\branch\BranchPlugin;
use pocketmine\scheduler\AsyncTask;

abstract class QueryAsyncTask extends AsyncTask{
	const STORE_MYSQLI_KEY = "legionpe.branch.query.conn";
	protected function getMySQLi(){
		/** @var \mysqli $mysqli */
//		$mysqli = $this->getFromThreadStore(self::STORE_MYSQLI_KEY);
//		if($mysqli === null){
			$class = BranchPlugin::$implementation;
			/** @var \legionpe\branch\Credentials $credentials */
			/** @noinspection PhpUndefinedMethodInspection */
			$credentials = $class::getCredentials();
			$mysqli = $credentials->getMySQLi();
//			$this->saveToThreadStore(self::STORE_MYSQLI_KEY, $mysqli);
//		}
		return $mysqli;
	}
}
