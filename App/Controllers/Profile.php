<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;

/**
 * Profile data controller
 */
class Profile extends Authenticated{
	
	protected function before(){
		parent::before();
		$this->user = Auth::getUser();
	}
	
	/**
	 * Show the profile
	 *
	 * @return void
	 */
	public function showAction(){
		View::renderTemplate('Profile/show.html', [
			'user' => $this->user
		]);
	}
	
	/**
	 * Show the form for editing the profile
	 *
	 * @return void
	 */
	public function editAction(){
		View::renderTemplate('Profile/edit.html', [
			'user' => $this->user
		]);
	}
	
	/**
	 * Update the profile
	 *
	 * @return void
	 */
	public function updateAction(){
		
		if($this->user->updateProfile($_POST)){
			Flash::addMessage('Zapisano zmiany.');
			$this->redirect('/profile/show');
		}
		else{
			View::renderTemplate('Profile/edit.html', [
				'user' => $this->user
			]);
		}
	}
}