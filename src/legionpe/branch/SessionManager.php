<?php

namespace legionpe\branch;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;

class SessionManager implements Listener{
	/** @var BranchPlugin */
	private $plugin;
	private $sessions = [];
	public function __construct(BranchPlugin $plugin){
		$this->plugin = $plugin;
	}
	public function onJoin(PlayerJoinEvent $event){
		$this->sessions[$event->getPlayer()->getId()] = new Session($this->plugin, $event->getPlayer());
		$event->setJoinMessage("");
	}
}
