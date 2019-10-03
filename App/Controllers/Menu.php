<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;

/**
 * Menu controller
 *
 */
class Menu extends Authenticated{
	/**
	 * Show the menu page
	 *
	 * @return void
	 */
	public function indexAction(){
		View::renderTemplate('Menu/index.html', [
			'no_navbar' => true
		]);
	}

}