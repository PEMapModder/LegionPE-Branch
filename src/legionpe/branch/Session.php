<?php

namespace legionpe\branch;

use pocketmine\Player;

abstract class Session{
	const STATUS_LOGGING = 2;
	const STATUS_NORMAL = 3;
	/** @var Player */
	private $player;
	/** @var int */
	private $uid;
	protected $status = self::STATUS_LOGGING;
	private $mysqlBasicData = [];
	public function __construct(Player $player, $uid, $basicData){
		$this->player = $player;
		$this->uid = $uid;
		$this->mysqlBasicData = $basicData;
	}
	/**
	 * @return Player
	 */
	public function getPlayer(){
		return $this->player;
	}
	/**
	 * @return int
	 */
	public function getUid(){
		return $this->uid;
	}
	public function isLoggedIn(){
		return $this->status === self::STATUS_NORMAL;
	}
	public function finalize(){
		// TODO: Save data.
	}
}
