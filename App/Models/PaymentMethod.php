<?php

namespace App\Models;

use PDO;

/**
 * Payment method model
 *
 */
class PaymentMethod extends \App\Category{
	
	const SOURCE_TABLE_NAME = "payment_methods";
	
	public static function getUserPaymentMethods($userID){
		return static::getUsersCategories($userID);
	}
}