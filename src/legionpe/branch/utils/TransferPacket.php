<?php

namespace legionpe\branch\utils;

use pocketmine\network\protocol\DataPacket;

class TransferPacket extends DataPacket{
	public function pid(){
		return 0x1b;
	}
	protected function putAddress($addr, $port, $version = 4){
		$this->putByte($version);
		if($version === 4){
			foreach(explode(".", $addr) as $b){
				$this->putByte(~((int) $b));
			}
			$this->putShort($port);
		}else{
			//IPv6
		}
	}
	public function decode(){
	}
	public function encode(){
		$this->reset();
		$this->putAddress($this->address, $this->port);
	}
}
