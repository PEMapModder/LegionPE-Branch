<?php

namespace legionpe\branch;

use legionpe\branch\queries\SyncQuery;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerPreLoginEvent;

class BaseEventListener implements Listener{
	/** @var BranchPlugin */
	private $main;
	public function __construct(BranchPlugin $main){
		$this->main = $main;
	}
	public function onPreLogin(PlayerPreLoginEvent $event){
		$player = $event->getPlayer();
		$name = $player->getName();
		$uuid = $player->getUniqueId();
		$query = new SyncQuery($this->main, "SELECT uid,status,lastses,authuuid,rank FROM users WHERE name='{$this->main->escStr($name)}'", SyncQuery::TYPE_ASSOC, [
			"uid" => SyncQuery::COL_INT,
			"status" => SyncQuery::COL_INT,
			"lastses" => SyncQuery::COL_INT,
			"authuuid" => SyncQuery::COL_STRING,
			"rank" => SyncQuery::COL_INT
		]);
		$query->exe();
		if($query->result["success"]){
			$data = $query->result["result"];
			if(!is_array($data)){
				return; // normal registration
			}
			if($data["status"] === Session::STATUS_TRANSFER and $data["authuuid"] === $uuid){
				$event->setCancelled(false); // :( we promised him he could join
			}
		}
	}
}
