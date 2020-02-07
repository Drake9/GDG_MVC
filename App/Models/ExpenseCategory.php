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
	
	
	public static function getUsersCategories($userID){
		return static::getUserExpenseCategories($userID);
	}
	
	/**
     * Get user's all expense categories with amount limit left
	 *
	 * @param int userID
     *
     * @return 
     */
	public static function getUserExpenseCategories($userID){
		
		$db = static::getDB();
		
		//$sql = "SELECT results.id, results.user_id, results.name, results.amount_limit, results.amount_limit - SUM(results.amount) AS limit_left FROM (SELECT cat.id, cat.user_id, cat.name, cat.amount_limit, expenses.amount FROM `expense_categories` as cat INNER JOIN `expenses` ON cat.id = expenses.category WHERE YEAR(expenses.date) = YEAR(CURDATE()) AND MONTH(expenses.date) = MONTH(CURDATE()) AND cat.user_id = :userID) AS results GROUP BY results.id ";
		
		$sql = "SELECT results.id, results.user_id, results.name, results.amount_limit, results.amount_limit - SUM(results.amount) AS limit_left FROM (SELECT cat.id, cat.user_id, cat.name, cat.amount_limit, expenses.amount, expenses.date FROM `expense_categories` as cat LEFT JOIN `expenses` ON cat.id = expenses.category AND YEAR(expenses.date) = YEAR(CURDATE()) AND MONTH(expenses.date) = MONTH(CURDATE()) WHERE cat.user_id = :userID ) AS results GROUP BY results.id";
			
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
		
		parent::validate();
		
		if (isset($this->amount_limit)){
			
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