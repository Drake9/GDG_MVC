<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;

/**
 * Items controller
 *
 */
class Expense extends Authenticated{
	
	/**
	 * Adding new income
	 *
	 * @return void
	 */
	 public function newAction(){
		 View::renderTemplate('Expense/new.html');
	 }
}