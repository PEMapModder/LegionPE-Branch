<?php

namespace legionpe\branch;

use pocketmine\plugin\PluginBase;

abstract class BranchPlugin extends PluginBase{
	const HUB = 0;
	const KITPVP = 1;
	const PARKOUR = 2;
	const SPLEEF = 3;
	const INFECTED = 4;
	const CLASSIC_PVP = 5; // <<< classic :)
	/** @var \mysqli */
	private $mysql;
	/** @var BaseEventListener */
	private $listener;
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
		$this->mysql->query("INSERT INTO server_status (server_id, ip, port, class, last_online, mypid) VALUES ($id, '$ip', $port, $class, unix_timestamp(), $mypid) ON DUPLICATE KEY UPDATE ip='$ip', port=$port, class=$class, last_online=unix_timestamp(), mypid=$mypid");
	}
	/**
	 * @return BaseEventListener
	 */
	public function getListener(){
		return $this->listener;
	}
	/**
	 * @return \mysqli
	 */
	public function getMysql(){
		return $this->mysql;
	}

	private function initMysql(){
		try{
			$this->mysql = new \mysqli(static::getMysqlHost(), static::getMysqlUser(), static::getMysqlPass(), static::getMysqlSchema(), static::getMysqlPort());
		}catch(\RuntimeException $e){
			$this->getLogger()->critical("Failed to connect to MySQL: " . $e->getMessage());
			exit(2);
		}
	}
	public function escStr($str){
		return is_string($str) ? "'" . $this->mysql->escape_string($str) . "'" : "$str";
	}

	/** @var string */
	private static $impl;
	/**
	 * @return string
	 */
	public static function currentImpl(){
		return self::$impl;
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
