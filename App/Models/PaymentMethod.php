<?php

namespace App\Models;

use PDO;

/**
 * Payment method  model
 *
 */
class PaymentMethod extends \Core\Model{
	
	/**
     * Class constructor
	 *
	 *@param array $data  Initial property values
     *
     * @return void
     */
    public function __construct($data = []){
        foreach($data as $key => $value){
			$this->$key = $value;
		}
    }
	
	/**
     * Get user's all payment methods
	 *
	 * @param int userID
     *
     * @return 
     */
	public static function getUserPaymentMethods($userID){
		
		$db = static::getDB();
		
		$sql = "SELECT * FROM payment_methods WHERE user_id = :userID";
			
		$query = $db->prepare($sql);
		
		$query->bindValue(':userID', $userID, PDO::PARAM_INT);
		
		$query->execute();
		
		return $query->fetchAll();
	}
	
	/**
     * Save model with the current property values
     *
     * @return 
     */
	public function save(){
		
		$db = static::getDB();
		
		$sql = "INSERT INTO payment_methods VALUES (NULL, :userID, :name)";
			
		$query = $db->prepare($sql);
		
		$query->bindValue(':userID', $this->userID, PDO::PARAM_INT);
		$query->bindValue(':name', $this->name, PDO::PARAM_STR);
		
		return $query->execute();
	}
}