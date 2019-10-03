<?php

namespace App\Models;

use App\Token;

use PDO;

/**
 * Remember login model
 */
class RememberedLogin extends \Core\Model{
	
	/**
	 * Find a remembered login model by the token
	 *
	 * @param string the remembered login token
	 *
	 * @return mixed RememberedLogin object if found, false otherwise
	 */
	public static function findByToken($token){
		 
		$token = new Token($token);
		$tokenHash = $token->getHash();
		 
		$db = static::getDB();
		
		$sql = 'SELECT * FROM remembered_logins WHERE token_hash = :tokenHash';
			
		$query = $db->prepare($sql);
		
		$query->bindValue(':tokenHash', $tokenHash, PDO::PARAM_STR);
		
		$query->setFetchMode(PDO::FETCH_CLASS, get_called_class());
		
		$query->execute();
		
		return $query->fetch();
	 }
	 
	 /**
     * Get the user model associated with remembered login
     *
     * @return User
     */
	public function getUser(){	
		
		return User::findByID($this->user_id);
    }
	
	/**
     * See if token has expired or not
     *
     * @return boolean true if expired, false otherwise
     */
	public function hasExpired(){	
		
		return strtotime($this->expires_at) < time();
    }
	
	/**
     * Delete this model
     *
     * @return void
     */
	public function delete(){	
		
		$db = static::getDB();
		
		$sql = 'DELETE FROM remembered_logins WHERE token_hash = :tokenHash';
			
		$query = $db->prepare($sql);
		
		$query->bindValue(':tokenHash', $this->token_hash, PDO::PARAM_STR);
		
		$query->execute();
    }
}