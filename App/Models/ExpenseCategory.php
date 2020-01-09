<?php

namespace App\Models;

use PDO;

/**
 * Expense category model
 *
 */
class ExpenseCategory extends \App\Category{
	
	const SOURCE_TABLE_NAME = "expense_categories";
	public $errorAmountLimit = null;
	
	public static function getUserExpenseCategories($userID){
		return static::getUsersCategories($userID);
	}
	
	/**
     * Save model with the current property values
     *
     * @return 
     */
	public function save(){
		
		$this->validate();
		
		if($this->success){
			
			$db = static::getDB();
			
			$sql = "INSERT INTO ".static::SOURCE_TABLE_NAME." VALUES (NULL, :userID, :name, :amount_limit)";
				
			$query = $db->prepare($sql);
			
			$query->bindValue(':userID', $this->user_id, PDO::PARAM_INT);
			$query->bindValue(':name', $this->name, PDO::PARAM_STR);
			
			if(isset($this->amount_limit))
				$query->bindValue(':amount_limit', $this->amount_limit, PDO::PARAM_STR);
			else
				$query->bindValue(':amount_limit', null, PDO::PARAM_INT);
			
			if (!$query->execute()){
				$this->success = false;
				$this->errorQuery = "SQL error";
			}
			
		}
		
		return $this;
	}
	
	/**
     * Validate current property values, adding validation error messages to the errors array property
     *
     * @return void
     */
	public function validate(){
		
		if ($this->name == ''){
			$this->errorName = "Nazwa jest wymagana.";
		}
		else if ((strlen($this->name) < 3) || (strlen($this->name) > 50)){
			$this->errorName = "Nazwa musi posiadać od 3 do 50 znaków!";
		}
		else{
			if( isset($this->id) ){
				if( static::nameExists($this->user_id, $this->name, $this->id) ){
					$this->errorName = "Istnieje już kategoria o podanej nazwie.";
				}
			}
			else{
				if( static::nameExists($this->user_id, $this->name) ){
					$this->errorName = "Istnieje już kategoria o podanej nazwie.";
				}
			}
		}
		
		if (isset($this->amount_limit)){
			//$this->amount_limit = str_replace(',', '.', $this->amount_limit);
			
			if (!is_numeric($this->amount_limit))
				$this->errorAmountLimit = "Podaj prawidłową kwotę.";
			
			if ($this->amount_limit < 0)
				$this->errorAmountLimit = "Kwota limitu nie może być ujemna.";
		}
		
		if ($this->errorName != null || $this->errorAmountLimit != null){
			$this->success = false;
		}

	}
	
	/**
     * Update model with the current property values
     *
     * @return 
     */
	public function update(){
		
		$this->validate();
		
		if($this->success){
			
			$db = static::getDB();
			
			$sql = "UPDATE ".static::SOURCE_TABLE_NAME." SET name = :name, amount_limit = :amount_limit WHERE id = :id AND user_id = :userID";
				
			$query = $db->prepare($sql);
			
			$query->bindValue(':id', $this->id, PDO::PARAM_INT);
			$query->bindValue(':userID', $this->user_id, PDO::PARAM_INT);
			$query->bindValue(':name', $this->name, PDO::PARAM_STR);
			
			if(isset($this->amount_limit))
				$query->bindValue(':amount_limit', $this->amount_limit, PDO::PARAM_STR);
			else
				$query->bindValue(':amount_limit', null, PDO::PARAM_INT);
			
			if (!$query->execute()){
				$this->success = false;
				$this->errorQuery = "SQL error";
			}
		}
		
		return $this;
	}
}