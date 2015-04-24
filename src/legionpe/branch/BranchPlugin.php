<?php

namespace legionpe\branch;

use pocketmine\plugin\PluginBase;

abstract class BranchPlugin extends PluginBase{
	/** @var string */
	private static $impl;
	/** @var \mysqli */
	private $mysql;
	/**
	 * @return string
	 */
	public static function currentImpl(){
		return self::$impl;
	}
	/**
	 * Validate the implementation of BranchPlugin
	 */
	public function onLoad(){
		self::$impl = static::class;
		static::getMysqlHost();
		static::getMysqlUser();
		static::getMysqlPass();
		static::getMysqlSchema();
	}
	public function onEnable(){
		$this->saveDefaultConfig();
		$this->initMysql();
		$id = $this->getConfig()->get("server-id");
		$ip = $this->getConfig()->get("ip");
		$port = $this->getConfig()->get("port");
		$class = $this->getConfig()->get("class");
		$mypid = getmypid();
		$this->mysql->query("INSERT INTO active_servers(sid,address,port,last_up,cnt,kitpvp,parkour,spleef,infected,pid,restart)VALUES($id,{$this->escStr($ip)},$port,CURRENT_TIMESTAMP,0,0,0,0,0,{$this->escStr($mypid)},1)ON DUPLICATE KEY UPDATE address={$this->escStr($ip)},port=$port,last_up=CURRENT_TIMESTAMP"); // the kitpvp,parkour,etc. columns are just for initialization. They will be deprecated in LegionPE-Branch implementations because each branch should only own one game.
	}
	private function initMysql(){
		try{
			$this->mysql = new \mysqli(static::getMysqlHost(), static::getMysqlUser(), static::getMysqlPass(), static::getMysqlSchema(), static::getMysqlPort());
		}catch(\RuntimeException $e){
			$this->getLogger()->critical("Failed to connect to MySQL: " . $e->getMessage());
			exit(2);
		}
	}
	private function escStr($str){
		return is_string($str) ? "'" . $this->mysql->escape_string($str) . "'" : "$str";
	}
	/**
	 * @return string
	 */
	public static function getMysqlHost(){
		throw new \RuntimeException("Implementation of BranchPlugin " . static::class . " didn't implement BranchPlugin::" . __METHOD__ . "()!");
	}
	/**
	 * @return string
	 */
	public static function getMysqlUser(){
		throw new \RuntimeException("Implementation of BranchPlugin " . static::class . " didn't implement BranchPlugin::" . __METHOD__ . "()!");
	}
	/**
	 * @return string
	 */
	public static function getMysqlPass(){
		throw new \RuntimeException("Implementation of BranchPlugin " . static::class . " didn't implement BranchPlugin::" . __METHOD__ . "()!");
	}
	/**
	 * @return string
	 */
	public static function getMysqlSchema(){
		throw new \RuntimeException("Implementation of BranchPlugin " . static::class . " didn't implement BranchPlugin::" . __METHOD__ . "()!");
	}
	/**
	 * @return int
	 */
	public static function getMysqlPort(){
		return 3306;
	}
}
