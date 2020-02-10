<?php

namespace App\Models;

use PDO;
use DateTime;

/**
 * Expense model
 *
 * PHP version 7.0
 */
class Expense extends \Core\Model{
	/**
     * Error messages
	 *
	 * @var array
     *
     */
    public $errorsAmount = [];
    public $errorsDate = [];
    public $errorsCategory = [];
    public $errorsMethod = [];
    public $errorsComment = [];
	
	/**
     * Class constructor
	 *
	 * @param array $data  Initial property values
     *
     * @return void
     */
    public function __construct($data = []){
        foreach($data as $key => $value){
			$this->$key = $value;
		}
    }

    /**
     * Save the income model with the current property values
     *
     * @return boolean
     */
    public function save(){
		
		$this->validate();
		
		if( empty($this->errorsAmount) && empty($this->errorsDate) && empty($this->errorsCategory) && empty($this->errorsMethod) && empty($this->errorsComment) ){
			
			$db = static::getDB();

			$sql = 'INSERT INTO expenses (user_id, amount, date, category, method, comment)
				VALUES (:userID, :amount, :date, :category, :method, :comment)';
				
			$query = $db->prepare($sql);
			
			$query->bindParam(':userID', $this->userID);
			$query->bindParam(':amount', $this->amount);
			$query->bindParam(':date', $this->date);
			$query->bindParam(':category', $this->category);
			$query->bindParam(':method', $this->method);
			$query->bindParam(':comment', $this->comment);

			
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
		
        /**
		 * Amount
		 */
		if ($this->amount <= 0){
			$this->errorsAmount[] = "Kwota musi być większa od zera.";
		}
		
		/**
		 * Date
		 */
		$format = 'Y-m-d';
		$date = DateTime::createFromFormat($format, $this->date);
		
		if( !( $date && $date->format($format) === $this->date )){
			$this->errorsDate[] = "Podana data nie jest prawidłowa.";
		}		
		
		/**
		 * Category
		 */	
		$incomeCategories = ExpenseCategory::getUserExpenseCategories($this->userID);
		$flagOK = false;
		
		foreach($incomeCategories as $category){
			if($category['id'] == $this->category){
				$flagOK = true;
				break;
			}
		}
		
		if( !$flagOK ){
			$this->errorsCategory[] = "Podana kategoria jest nieprawidłowa.";
		}
		
		/**
		 * Method
		 */	
		$paymentMethods = PaymentMethod::getUserPaymentMethods($this->userID);
		$flagOK = false;
		
		foreach($paymentMethods as $method){
			if($method['id'] == $this->method){
				$flagOK = true;
				break;
			}
		}
		
		if( !$flagOK ){
			$this->errorsCategory[] = "Podana kategoria jest nieprawidłowa.";
		}

		/**
		 * Comment
		 */
		if(strlen($this->comment) > 100){
			$this->errorsComment[] = "Podany komentarz jest za długi.";
		}
		else if($this->comment == ""){
			$this->comment = "-";
		}			

    }
	
	/**
     * Checks if amount exceeds limit
     *
     * @return void
     */
	private function validateAmountLimit(){
		
		$limit = $this->getAmountLimitLeft();
		
		if( $limit != null && $this->amount > $limit ){
			$this->errorsAmount[] = "Pozostały limit na wydatki z tej kategorii w tym miesiącu wynosi: ".$limit." zł.";
		}
	}
	
	/**
     * Get amount limit left for this month for this expense's category
     *
     * @return null or decimal
     */
	public function getAmountLimitLeft(){
		$db = static::getDB();
		
		$sql = "SELECT amount_limit AS monthly_limit FROM expense_categories WHERE id = :categoryID";
		$query = $db->prepare($sql);
		$query->bindValue(':categoryID', $this->category, PDO::PARAM_INT);
		$query->execute();
		$monthlyLimit = $query->fetch();
		
		if($monthlyLimit['monthly_limit'] == null){
			return null;
		}
		
		$sql = "SELECT SUM(results.amount) AS amount_spent FROM (SELECT cat.id, cat.user_id, cat.name, cat.amount_limit, expenses.amount, expenses.date FROM `expense_categories` as cat LEFT JOIN `expenses` ON cat.id = expenses.category AND YEAR(expenses.date) = YEAR(:date) AND MONTH(expenses.date) = MONTH(:date)";
		
		if( isset($this->id) ){
			$sql .= " AND expenses.id != :ignoredID";
		}
		
		$sql .= " WHERE cat.user_id = :userID AND expenses.category = :categoryID) AS results GROUP BY results.id";
			
		$query = $db->prepare($sql);
		
		if( isset($this->id) ){
			$query->bindValue(':ignoredID', $this->id, PARAM_INT);
		}
			
		$query->bindValue(':userID', $this->userID, PDO::PARAM_INT);
		$query->bindValue(':categoryID', $this->category, PDO::PARAM_INT);
		$query->bindValue(':date', $this->date, PDO::PARAM_STR);
		
		$query->execute();
		
		$amountSpent = $query->fetch();
		
		$limitLeft = $monthlyLimit['monthly_limit'] - $amountSpent['amount_spent'];
		return $limitLeft;
	}
	
	/**
     * Update the expense model with the current property values
     *
     * @return boolean
     */
    public function update(){
		
		$this->validate();
		
		if( empty($this->errorsAmount) && empty($this->errorsDate) && empty($this->errorsCategory) && empty($this->errorsMethod) && empty($this->errorsComment) ){
			
			$db = static::getDB();

			$sql = 'UPDATE expenses SET amount = :amount, date = :date, category = :category, method = :method, comment = :comment
				WHERE id = :id AND user_id = :userID';
				
			$query = $db->prepare($sql);
			
			$query->bindParam(':id', $this->id);
			$query->bindParam(':userID', $this->userID);
			$query->bindParam(':amount', $this->amount);
			$query->bindParam(':date', $this->date);
			$query->bindParam(':category', $this->category);
			$query->bindParam(':method', $this->method);
			$query->bindParam(':comment', $this->comment);

			
			return $query->execute();
		}
		
		return false;
    }
	
	/**
     * Delete the income model
     *
     * @return boolean
     */
    public function delete(){
		
		$db = static::getDB();

		$sql = 'DELETE FROM expenses WHERE id = :id AND user_id = :userID';
			
		$query = $db->prepare($sql);
		
		$query->bindParam(':id', $this->id);
		$query->bindParam(':userID', $this->userID);
		
		return $query->execute();
    }
	
	/**
     * See if a category is in use
	 *
	 * @param int $categoryID to search for
     *
     * @return boolean TRUE if exists, FALSE otherwise
     */
	public static function isCategoryInUse($categoryID){
		
		return static::findByExpenseCategory($categoryID) !== false;
    }
	
	/**
     * Find an expense model by expense category
	 *
	 * @param int categoryID to search for
     *
     * @return mixed ExpenseCategory object if found, false otherwise
     */
    public static function findByExpenseCategory($categoryID){
		
		$db = static::getDB();

		$sql = 'SELECT * FROM expenses WHERE category = :categoryID';
			
		$query = $db->prepare($sql);
		
		$query->bindValue(':categoryID', $categoryID, PDO::PARAM_INT);
		
		$query->setFetchMode(PDO::FETCH_CLASS, get_called_class());
		
		$query->execute();
		
		return $query->fetch();
    }
	
	/**
     * See if a payment method is in use
	 *
	 * @param int $methodID to search for
     *
     * @return boolean TRUE if exists, FALSE otherwise
     */
	public static function isMethodInUse($methodID){
		
		return static::findByPaymentMethod($methodID) !== false;
    }
	
	/**
     * Find an expense model by payment method
	 *
	 * @param int methodID to search for
     *
     * @return mixed PaymentMethod object if found, false otherwise
     */
    public static function findByPaymentMethod($methodID){
		
		$db = static::getDB();

		$sql = 'SELECT * FROM expenses WHERE method = :methodID';
			
		$query = $db->prepare($sql);
		
		$query->bindValue(':methodID', $methodID, PDO::PARAM_INT);
		
		$query->setFetchMode(PDO::FETCH_CLASS, get_called_class());
		
		$query->execute();
		
		return $query->fetch();
    }
}
