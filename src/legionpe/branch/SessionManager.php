<?php

namespace legionpe\branch;

use pocketmine\Player;

class SessionManager{
	/** @var Session[] */
	private $sessions = [];
	/** @var BranchPlugin */
	private $main;
	public function __construct(BranchPlugin $main){
		$this->main = $main;
	}
	public function addSession(Session $session){
		$this->sessions[$session->getPlayer()->getId()] = $session;
	}
	public function endSession(Player $player){
		if(isset($this->sessions[$player->getId()])){
			$this->sessions[$player->getId()]->finalize();
			unset($this->sessions[$player->getId()]);
		}
	}
	/**
	 * @param string|Player $player
	 * @return Session|null
	 */
	public function get($player){
		if(!($player instanceof Player)){
			$player = $this->main->getServer()->getPlayer($player);
		}
		if(!($player instanceof Player)){
			return null;
		}
		return isset($this->sessions[$player->getId()]) ? $this->sessions[$player->getId()] : null;
	}
}
