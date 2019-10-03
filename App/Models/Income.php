<?php

namespace App\Models;

use PDO;
use DateTime;

/**
 * Income model
 *
 * PHP version 7.0
 */
class Income extends \Core\Model{
	/**
     * Error messages
	 *
	 * @var array
     *
     */
    public $errorsAmount = [];
    public $errorsDate = [];
    public $errorsCategory = [];
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
		
		if( empty($this->errorsAmount) && empty($this->errorsDate) && empty($this->errorsCategory) && empty($this->errorsComment) ){
			
			$db = static::getDB();

			$sql = 'INSERT INTO incomes (user_id, amount, date, category, comment)
				VALUES (:userID, :amount, :date, :category, :comment)';
				
			$query = $db->prepare($sql);
			
			$query->bindParam(':userID', $this->userID);
			$query->bindParam(':amount', $this->amount);
			$query->bindParam(':date', $this->date);
			$query->bindParam(':category', $this->category);
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
		$incomeCategories = IncomeCategory::getUserIncomeCategories($this->userID);
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
		 * Comment
		 */
		if(strlen($this->comment) > 100){
			$this->errorsComment[] = "Podany komentarz jest za długi.";
		}
		else if($this->comment == ""){
			$this->comment = "-";
		}			

    }	
	
}
