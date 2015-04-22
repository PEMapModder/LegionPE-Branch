<?php

namespace legionpe\branch;

use pocketmine\plugin\PluginBase;

abstract class BranchPlugin extends PluginBase{
	public static $implementation = null;
	/** @var MysqlDb */
	private $mysql;
	/** @var SessionManager */
	private $sesMgr;
	public function onLoad(){
		self::$implementation = static::class;
	}
	public function onEnable(){
		$credentials = static::getCredentials();
		$this->mysql = new MysqlDb($credentials);
		$this->getServer()->getPluginManager()->registerEvents($this->sesMgr = new SessionManager($this), $this);
	}
	/**
	 * @return Credentials
	 */
	protected static function getCredentials(){
		$me = static::class;
		throw new \RuntimeException("getCredentials method has not been implemented by $me.");
	}
}
