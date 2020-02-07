<?php

namespace App\Models;

use PDO;

/**
 * Balance model
 *
 */
class Balance extends \Core\Model{
	
	private $userID;
	private $dateStart;
	private $dateEnd;
	
	/**
     * Class constructor
	 *
	 * @param int userID
	 * @param string date of the beginning
	 * @param string date of the end
     *
     * @return void
     */
    public function __construct($userID, $dateStart, $dateEnd){
        $this->userID = $userID;
		$this->dateStart = $dateStart;
		$this->dateEnd = $dateEnd;
    }
	
	/**
	* Get sums of income categories
	*
	* @return mixed
	*/
	public function getIncomesByCategories(){
		
		$db = static::getDB();
		
		$sql = "SELECT cat.name AS category, SUM(inc.amount) AS amount
			FROM `income_categories` AS cat, `incomes` AS inc
			WHERE inc.user_id = :userID
			AND inc.category = cat.id
			AND inc.date BETWEEN :dateStart AND :dateEnd
			GROUP BY inc.category
			ORDER BY amount DESC";
			
		$query = $db->prepare($sql);
		
		$query->bindValue(':userID', $this->userID);
		$query->bindValue(':dateStart', $this->dateStart);
		$query->bindValue(':dateEnd', $this->dateEnd);
		
		$query->execute();
		
		return $query->fetchAll();
	}
	
	/**
	* Get sums of expense categories
	*
	* @return mixed
	*/
	public function getExpensesByCategories(){
		
		$db = static::getDB();
		
		$sql = "SELECT cat.name AS category, SUM(exp.amount) AS amount
			FROM `expense_categories` AS cat, `expenses` AS exp
			WHERE exp.user_id = :userID
			AND exp.category = cat.id
			AND exp.date BETWEEN :dateStart AND :dateEnd
			GROUP BY exp.category
			ORDER BY amount DESC";
			
		$query = $db->prepare($sql);
		
		$query->bindValue(':userID', $this->userID);
		$query->bindValue(':dateStart', $this->dateStart);
		$query->bindValue(':dateEnd', $this->dateEnd);
		
		$query->execute();
		
		return $query->fetchAll();
	}
	
	/**
	* Get detailed incomes
	*
	* @return mixed
	*/
	public function getDetailedIncomes(){
		
		$db = static::getDB();
		
		$sql = "SELECT inc.id, inc.date, cat.name as category, cat.id as category_id, inc.comment, inc.amount
			FROM `incomes` AS inc, `income_categories` AS cat
			WHERE inc.user_id = :userID
			AND inc.category = cat.id
			AND inc.date BETWEEN :dateStart AND :dateEnd
			ORDER BY inc.date";
			
		$query = $db->prepare($sql);
		
		$query->bindValue(':userID', $this->userID);
		$query->bindValue(':dateStart', $this->dateStart);
		$query->bindValue(':dateEnd', $this->dateEnd);
		
		$query->execute();
		
		return $query->fetchAll();
	}
	
	/**
	* Get detailed expenses
	*
	* @return mixed
	*/
	public function getDetailedExpenses(){
		
		$db = static::getDB();
		
		$sql = "SELECT exp.id, exp.date, cat.name as category, cat.id as category_id, exp.comment, exp.amount, exp.method, met.id as method_id
			FROM `expenses` AS exp, `expense_categories` AS cat, `payment_methods` AS met
			WHERE exp.user_id = :userID
			AND exp.category = cat.id
			AND exp.method = met.id
			AND exp.date BETWEEN :dateStart AND :dateEnd
			ORDER BY exp.date";
			
		$query = $db->prepare($sql);
		
		$query->bindValue(':userID', $this->userID);
		$query->bindValue(':dateStart', $this->dateStart);
		$query->bindValue(':dateEnd', $this->dateEnd);
		
		$query->execute();
		
		return $query->fetchAll();
	}
}