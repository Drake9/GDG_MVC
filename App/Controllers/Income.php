<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\IncomeCategory;
use \App\Models\Income as IncomeModel;
use \App\Flash;

/**
 * Income controller
 *
 */
class Income extends Authenticated{
	
	/**
	 * Show the add income page
	 *
	 * @return void
	 */
	public function newAction(){
		
		$incomeCategories = IncomeCategory::getUserIncomeCategories($_SESSION['userID']);
		
		View::renderTemplate('Income/new.html', [
			'income' => "new",
			'income_categories' => $incomeCategories
		]);
	}
	
	/**
     * Add new income
     *
     * @return void
     */
	public function createAction(){
		$income = new IncomeModel($_POST);
		$income->userID = $_SESSION['userID'];
		
		if ($income->save()){
			Flash::addMessage('Nowy dochód został dodany pomyślnie.');
			$this->redirect('/income/new');
		}
		else{
			$incomeCategories = IncomeCategory::getUserIncomeCategories($_SESSION['userID']);
			Flash::addMessage('Podane dane są nieprawidłowe.', Flash::WARNING);
			View::renderTemplate('Income/new.html', [
				'income' => $income,
				'income_categories' => $incomeCategories
			]);
		}
	}
	
	/**
     * Edit income - ajax request
     *
     * @return json
     */
	public function editAction(){
		$income = new IncomeModel($_POST);
		$income->userID = $_SESSION['userID'];
		
		if ($income->update()){
			$income->success = true;
		}
		else{
			$income->success = false;
		}
		
		echo json_encode($income);
	}
	
	/**
     * Delete income - ajax request
     *
     * @return json
     */
	public function deleteAction(){
		$income = new IncomeModel($_POST);
		$income->userID = $_SESSION['userID'];
		
		if ($income->delete()){
			$income->success = true;
		}
		else{
			$income->success = false;
		}
		
		echo json_encode($income);
	}
}