<?php

namespace legionpe\branch;

use legionpe\branch\queries\AccountQueryTask;
use pocketmine\Player;

class Session{
	const STATE_LOADING = 0;
	/** @var BranchPlugin */
	private $plugin;
	/** @var Player */
	private $player;
	/** @var int */
	private $state = self::STATE_LOADING;
	public function __construct(BranchPlugin $plugin, Player $player){
		$this->plugin = $plugin;
		$this->player = $player;
		$this->plugin->getServer()->getScheduler()->scheduleAsyncTask(new AccountQueryTask($this)); // TODO
	}
	/**
	 * @return int
	 */
	public function getSessionState(){
		return $this->state;
	}
}
