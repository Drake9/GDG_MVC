<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;

/**
 * Items controller
 *
 */
class Balance extends Authenticated{
	
	/**
	 * Adding new income
	 *
	 * @return void
	 */
	 public function viewAction(){
		 View::renderTemplate('/Balance/view.html');
	 }
}