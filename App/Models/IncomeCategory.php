<?php

namespace App\Models;

use PDO;

/**
 * Income category model
 *
 */
class IncomeCategory extends \Core\Model{
	
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
     * Get user's all income categories
	 *
	 * @param int userID
     *
     * @return 
     */
	public static function getUserIncomeCategories($userID){
		
		$db = static::getDB();
		
		$sql = "SELECT * FROM income_categories WHERE user_id = :userID";
			
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
		
		$sql = "INSERT INTO income_categories VALUES (NULL, :userID, :name)";
			
		$query = $db->prepare($sql);
		
		$query->bindValue(':userID', $this->userID, PDO::PARAM_INT);
		$query->bindValue(':name', $this->name, PDO::PARAM_STR);
		
		return $query->execute();
	}
}