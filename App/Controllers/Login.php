<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Auth;
use \App\Flash;

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
			Flash::addMessage('Logowanie pomyślne.');
			$this->redirect(Auth::getReturnToPage());
		}
		else{
			Flash::addMessage('Wystąpił błąd. Prosimy upewnić się, czy podane dane są poprawne, i spróbować ponownie.', Flash::WARNING);
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
		$this->redirect('/login/show-logout-message');
	}
	
	/**
	 * Show a "logged out" flash message and redirect to home page
	 *
	 * @return void
	 */
	public function showLogoutMessageAction(){
		Flash::addMessage('Nastąpiło wylogowanie.');
		$this->redirect('/');
	}
}