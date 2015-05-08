<?php

namespace legionpe\branch;

use legionpe\branch\queue\Runnable;
use legionpe\branch\queue\RunQueue;
use legionpe\branch\queue\RunQueueArray;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\AsyncTask;

abstract class BranchPlugin extends PluginBase{
	/**
	 * @param Player $player
	 * @return Session
	 */
	protected abstract function newSession(Player $player);
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
	public static function defaultUserRow(/** @noinspection PhpUnusedParameterInspection */
		$uid){
		// TODO
		return [];
	}

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
	/** @var SessionManager */
	private $sessions;
	/** @var RunQueue[] */
	private $queues = [];
	/** @var RunQueueArray */
	private $sessionRunQueue;
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
		$this->queues[] = $this->sessionRunQueue = new RunQueueArray($this);
		$this->sessions = new SessionManager($this);
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
	public function async(AsyncTask $task){
		$this->getServer()->getScheduler()->scheduleAsyncTask($task);
	}
	public function addToQueue(Runnable $runnable, $channel){
		if(!isset($this->queues[$channel])){
			$this->queues[$channel] = new RunQueue($this, $channel);
		}
		$this->queues[$channel]->add($runnable);
	}
	public function addForSessionQueue(Runnable $runnable, $sesId){
		$this->sessionRunQueue->sessionAdd($runnable, $sesId);
	}

	/**
	 * @return SessionManager
	 */
	public function getSessions(){
		return $this->sessions;
	}
	/**
	 * @param Player $player The new player
	 * @param mixed[]|null $baseRow The MySQL row fetched for the player
	 */
	public abstract function addSession(Player $player, $baseRow);
}
