<?php

namespace legionpe\branch\queue;

use legionpe\branch\BranchPlugin;
use pocketmine\scheduler\PluginTask;

class RunQueue extends PluginTask{
	const CHANNEL_SESSION = 0;
	/** @var BranchPlugin */
	private $main;
	/** @var int */
	private $channel;
	/** @var Runnable[] */
	protected $runnableList = [];
	public function __construct(BranchPlugin $main, $channel){
		parent::__construct($main);
		$this->main = $main;
		$this->channel = $channel;
		$this->main->getServer()->getScheduler()->scheduleDelayedRepeatingTask($this, 1, 1);
	}
	public function onRun($ticks){
		while(isset($this->runnableList[0])){
			if($this->runnableList[0]->execute($this->main)){
				array_shift($this->runnableList);
			}else{
				return;
			}
		}
	}
	public function add(Runnable $runnable){
		$this->runnableList[] = $runnable;
	}
	/**
	 * @return BranchPlugin
	 */
	public function getMain(){
		return $this->main;
	}
	/**
	 * @return int
	 */
	public function getChannel(){
		return $this->channel;
	}
}
