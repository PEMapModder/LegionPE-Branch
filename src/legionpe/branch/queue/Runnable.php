<?php

namespace legionpe\branch\queue;

use legionpe\branch\BranchPlugin;

class Runnable{
	public $ifCheck, $runCode;
	public function __construct(callable $if, callable $run){
		$this->ifCheck = $if;
		$this->runCode = $run;
	}
	public function execute(BranchPlugin $main){
		$if = $this->ifCheck;
		if($if($main)){
			$run = $this->runCode;
			$run($main);
			return true;
		}
		return false;
	}
}
