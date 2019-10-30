<?php

namespace App\Models;

use PDO;

/**
 * Income category model
 *
 */
class IncomeCategory extends \Core\Model{
	
	public $error = null;
	
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
		
		$this->validate();
		
		if($this->error == null){
			
			$db = static::getDB();
			
			$sql = "INSERT INTO income_categories VALUES (NULL, :userID, :name)";
				
			$query = $db->prepare($sql);
			
			$query->bindValue(':userID', $this->user_id, PDO::PARAM_INT);
			$query->bindValue(':name', $this->name, PDO::PARAM_STR);
			
			return $query->execute();
		}
		
		return false;
	}
	
	/**
     * Validate current property values, adding validation error messages to the errors array property
     *
     * @return void
     */
	public function validate(){
		
		if ($this->name == ''){
			$this->error = "Nazwa jest wymagana.";
		}
		else if ((strlen($this->name) < 3) || (strlen($this->name) > 50)){
			$this->error = "Nazwa musi posiadać od 3 do 50 znaków!";
		}
		else{
			if( isset($this->id) ){
				if( static::nameExists($this->user_id, $this->name, $this->id) ){
					$this->error = "Istnieje już kategoria o podanej nazwie.";
				}
			}
			else{
				if( static::nameExists($this->user_id, $this->name) ){
					$this->error = "Istnieje już kategoria o podanej nazwie.";
				}
			}
		}
	}
	
	/**
     * See if a category already exists in database
	 *
	 * @param int userID to search for
	 * @param string name to search for
     *
     * @return boolean TRUE if exists, FALSE otherwise
     */
	public static function nameExists($userID, $name, $ignoredID = 0){	
		
		return static::findByName($userID, $name, $ignoredID) !== false;
    }
	
	/**
     * Find a income category model by name
	 *
	 * @param int userID to search for
	 * @param string name to search for
     *
     * @return mixed IncomeCategory object if found, false otherwise
     */
	public static function findByName($userID, $name, $ignoredID){	
		$db = static::getDB();
		
		$sql = 'SELECT * FROM income_categories WHERE user_id = :userID AND lower(name) = lower(:name) AND id != :ignoredID';
			
		$query = $db->prepare($sql);
		
		$query->bindValue(':userID', $userID, PDO::PARAM_INT);
		$query->bindValue(':name', $name, PDO::PARAM_STR);
		$query->bindValue(':ignoredID', $ignoredID, PDO::PARAM_INT);
		
		$query->setFetchMode(PDO::FETCH_CLASS, get_called_class());
		
		$query->execute();
		
		return $query->fetch();
    }
	
	/**
     * Update model with the current property values
     *
     * @return 
     */
	public function update(){
		
		$this->validate();
		
		if($this->error == null){
			
			$db = static::getDB();
			
			$sql = "UPDATE income_categories SET name = :name WHERE id = :id AND user_id = :userID";
				
			$query = $db->prepare($sql);
			
			$query->bindValue(':id', $this->id, PDO::PARAM_INT);
			$query->bindValue(':userID', $this->user_id, PDO::PARAM_INT);
			$query->bindValue(':name', $this->name, PDO::PARAM_STR);
			
			return $query->execute();
		}
		
		return false;
	}
	
	/**
     * Delete model with the current property values
     *
     * @return 
     */
	public function delete(){
			
		$db = static::getDB();
		
		$sql = "DELETE FROM income_categories WHERE id = :id AND user_id = :userID AND name = :name";
			
		$query = $db->prepare($sql);
		
		$query->bindValue(':id', $this->id, PDO::PARAM_INT);
		$query->bindValue(':userID', $this->user_id, PDO::PARAM_INT);
		$query->bindValue(':name', $this->name, PDO::PARAM_STR);
		
		return $query->execute();

	}
}