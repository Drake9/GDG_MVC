<?php

namespace App\Controllers;

/**
 * Authenticated base controller
 */
abstract class Authenticated extends \Core\Controller{
	/**
     * Require the user to be authenticated
     *
     * @return void
     */
    protected function before(){
		$this->requireLogin();
    }
}