<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Auth;

/**
 * Login controller
 *
 */
class Login extends \Core\Controller{
	/**
	 * Show the login page
	 *
	 * @return void
	 */
	 public function newAction(){
		 View::renderTemplate('Login/new.html');
	 }
	 
	 /**
	 * Log user in
	 *
	 * @return void
	 */
	 public function createAction(){
		 
		 $user = User::authenticate($_POST['login'], $_POST['password']);
		 
		 if($user){
			 Auth::login($user);
			 $this->redirect(Auth::getReturnToPage());
		 }
		 else{
			 View::renderTemplate('Login/new.html', [
				'login' => $_POST['login']
			 ]);
		 }
	 }
	 
	 /**
	 * Log user out
	 *
	 * @return void
	 */
	 public function destroyAction(){
		Auth::logout();
		$this->redirect('/');
	 }
}