<?php

namespace App;

/**
 * Unique random tokens
 */
class Token{
	
	protected $token;
	
	public function __construct($tokenValue = null){
		if($tokenValue){
			$this->token = $tokenValue;
		}
		else{
			$this->token = bin2hex(random_bytes(16));
		}
	}
	
	public function getValue(){
		return $this->token;
	}
	
	public function getHash(){
		return hash_hmac('sha256', $this->token, \App\Config::SECRET_KEY);
	}
}