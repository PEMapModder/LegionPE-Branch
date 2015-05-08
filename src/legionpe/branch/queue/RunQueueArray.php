<?php

namespace legionpe\branch\queue;

use legionpe\branch\BranchPlugin;

class RunQueueArray extends RunQueue{
	const UNCLASSIFIED = -1;
	/** @var Runnable[][] */
	protected $runnableList = [];
	public function __construct(BranchPlugin $main, $channel = self::CHANNEL_SESSION){
		parent::__construct($main, $channel);
	}
	public function onRun($ticks){
		foreach($this->runnableList as $sesId => &$runnables){
			while(isset($runnables[0])){
				if($runnables[0]->execute($this->getMain())){
					array_shift($this->runnableList);
				}else{
					break;
				}
			}
			if(count($runnables) === 0){
				unset($this->runnableList[$sesId]);
			}
		}
	}
	public function add(Runnable $runnable){
		$this->sessionAdd($runnable, self::UNCLASSIFIED);
	}
	public function sessionAdd(Runnable $runnable, $sesId){
		if(!isset($this->runnableList[$sesId])){
			$this->runnableList[$sesId] = [$runnable];
		}else{
			$this->runnableList[$sesId][] = $runnable;
		}
	}
}
