<?php

namespace legionpe\branch;

use legionpe\branch\queries\IncrementIdQuery;
use legionpe\branch\queries\LoginQuery;
use legionpe\branch\queue\Runnable;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\utils\TextFormat;

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
		foreach($this->getMain()->getServer()->getOnlinePlayers() as $old){
			if(strtolower($player->getName()) === strtolower($old->getName())){
				if($old->getUniqueId() === $player->getUniqueId()){
					$old->close("Relog with the same UUID");
				}else{
					$oldSession = $this->getMain()->getSession($old);
					$loggedIn = ($oldSession instanceof Session) and ($oldSession->isLoggedIn());
					$loggedInStr = $loggedIn ? "logged in" : "not logged in";
					$player->kick(TextFormat::YELLOW . "Player with same name already online from another client.\n" .
						TextFormat::AQUA . "The IP of the online player ($loggedInStr) is " . TextFormat::DARK_PURPLE . $old->getAddress() . TextFormat::AQUA . ", and your IP is " . TextFormat::DARK_PURPLE . $player->getAddress() . TextFormat::AQUA . ".\n" .
						TextFormat::WHITE . $old->getAddress() . " isn't your IP? Get support from a human by sending an email to: " . TextFormat::GOLD . "support@legionpvp.eu", false);
				}
				break;
			}
		}
		$this->getMain()->async($queryTask = new LoginQuery($player->getName()));
		$this->getMain()->addForSessionQueue(new Runnable(function() use($queryTask){
			return $queryTask->hasResult();
		}, function(BranchPlugin $main) use($sesId, $queryTask){
			foreach($main->getServer()->getOnlinePlayers() as $p){
				if($p->getId() === $sesId){
					$result = $queryTask->getResult();
					if(is_array($result)){
						$rank = $result["rank"];
						if(($rank & 4) === 0 and count($this->getMain()->getServer()->getOnlinePlayers()) >= $this->getMain()->getServer()->getMaxPlayers()){
							$p->close("This server is full!\n" . TextFormat::AQUA . "ProTip: Donators can join any servers even if they are full.\n" . TextFormat::DARK_BLUE . "Donate today at http://lgpe.co/donate");
							return;
						}
						$main->addSession($p, $result);
					}else{
						$this->getMain()->async($uidTask = new IncrementIdQuery(IncrementIdQuery::USER));
						$main->addForSessionQueue(new Runnable(function() use($uidTask, $sesId){
							return $uidTask->hasResult();
						}, function(BranchPlugin $main) use($uidTask, $sesId){
							$uid = $uidTask->getResult()["value"];
							foreach($main->getServer()->getOnlinePlayers() as $p){
								if($p->getId() === $sesId){
									/** @var BranchPlugin $impl */
									$impl = BranchPlugin::currentImpl();
									$main->addSession($p, $impl::defaultUserRow($uid));
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
