<?php

namespace App\Models;

use PDO;

/**
 * Default options model
 *
 */
abstract class DefaultOptions extends \Core\Model{
	
	/**
     * Save all default options as a new user's options
	 *
	 * @param int userID 
	 * @param database connection - we want to avoid multiple connections in one transaction 
     *
     * @return boolean
     */
	public static function addAllDefaultOptions($userID, $db){
		
		$flag1 = static::addDefaultOptions($userID, $db, "default_expense_categories", "expense_categories");
		$flag2 = static::addDefaultOptions($userID, $db, "default_income_categories", "income_categories");
		$flag3 = static::addDefaultOptions($userID, $db, "default_payment_methods", "payment_methods");
		
		return $flag1 && $flag2 && $flag3;
	}
	
	/**
     * Save default categories as new user's categories
	 *
	 * @param int userID 
	 * @param database connection - we want to avoid multiple connections in one transaction
	 * @param string name of the source table
	 * @param string name of the destination table
     *
     * @return 
     */
	private static function addDefaultOptions($userID, $db, $sourceTable, $destinationTable){
		
		$categories = static::getDefaultOptions($db, $sourceTable);
		
		$sql = "INSERT INTO ".$destinationTable." VALUES";
		
		foreach($categories as $key => $category){
			if($key == 0){
				$sql .=  " (NULL, :userID, :name".$category['id'].")";
			}
			else{
				$sql .=  ", (NULL, :userID, :name".$category['id'].")";
			}
		}
		
		$query = $db->prepare($sql);
		
		foreach($categories as $category){
			$query->bindValue(':name'.$category['id'], $category['name'], PDO::PARAM_STR);
		}
		$query->bindValue(':userID', $userID, PDO::PARAM_STR);
		
		return $query->execute();
	}
	
	/**
     * Get default categories from DB
	 *
	 * @param database connection - we want to avoid multiple connections in one transaction
	 * @param string table name
     *
     * @return mixed table if success, false otherwise
     */
	private static function getDefaultOptions($db, $tableName){
		
		$sql = "SELECT * FROM ".$tableName;
			
		$query = $db->prepare($sql);
		
		$query->execute();
		
		return $query->fetchAll();
	}
	
}