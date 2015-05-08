<?php

namespace legionpe\branch;

use pocketmine\Player;

abstract class Session{
	const STATUS_OFFLINE = 0;
	const STATUS_FETCHING_DATA = 1;
	const STATUS_REGISTERING = 2;
	const STATUS_TRANSFER = 3;
	/** @var Player */
	private $player;
	/** @var int */
	private $uid;
	public function __construct(Player $player, $uid){
		$this->player = $player;
		$this->uid = $uid;
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
	public function finalize(){
		// TODO: Save data.
	}
}
