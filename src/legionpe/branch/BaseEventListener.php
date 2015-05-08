<?php

namespace legionpe\branch;

use legionpe\branch\queries\IncrementIdQuery;
use legionpe\branch\queries\LoginQuery;
use legionpe\branch\queue\Runnable;
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
		$sesId = $player->getId();

		$event->setCancelled(false);
		$this->getMain()->async($queryTask = new LoginQuery($player->getName()));
		$this->getMain()->addForSessionQueue(new Runnable(function() use($queryTask){
			return $queryTask->hasResult();
		}, function(BranchPlugin $main) use($sesId, $queryTask){
			foreach($main->getServer()->getOnlinePlayers() as $p){
				if($p->getId() === $sesId){
					$result = $queryTask->getResult();
					if(is_array($result)){
						$main->addSession($p, $result);
					}else{
						$this->getMain()->async($uidTask = new IncrementIdQuery(IncrementIdQuery::USER));
						$main->addForSessionQueue(new Runnable(function() use($uidTask, $sesId){
							return $uidTask->hasResult();
						}, function(BranchPlugin $main) use($uidTask, $sesId){
							$uid = $uidTask->getResult()["value"];
							foreach($main->getServer()->getOnlinePlayers() as $p){
								if($p->getId() === $sesId) {
									$main->addSession($p, BranchPlugin::defaultUserRow($uid));
									break;
								}
							}
						}), $sesId);
					}
					break;
				}
			}
		}), $sesId);
	}
	/**
	 * @return BranchPlugin
	 */
	public function getMain(){
		return $this->main;
	}
}

