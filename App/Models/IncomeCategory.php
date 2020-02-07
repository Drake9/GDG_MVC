<?php

namespace App\Models;

use PDO;

/**
 * Income category model
 *
 */
class IncomeCategory extends \App\Category{
	
	const SOURCE_TABLE_NAME = "income_categories";
	
	public static function getUserIncomeCategories($userID){
		return static::getUsersCategories($userID);
	}
	
}