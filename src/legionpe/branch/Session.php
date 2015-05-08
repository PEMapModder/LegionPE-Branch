<?php

namespace legionpe\branch;

use pocketmine\Player;

abstract class Session{
	const STATUS_OFFLINE = 0;
	const STATUS_ONLINE = 1;
	const STATUS_TRANSFER = 2;
	/** @var Player */
	private $player;
	/** @var int */
	private $uid;
	public function __construct(Player $player, $uid){
		$this->player = $player;
		$this->uid = $uid;
	}
}
