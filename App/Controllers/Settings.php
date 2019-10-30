<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\IncomeCategory;
use \App\Models\ExpenseCategory;
use \App\Models\PaymentMethod;

/**
 * Settings controller
 *
 */
class Settings extends Authenticated{
	
	/**
	 * Settings view
	 *
	 * @return void
	 */
	public function viewAction(){
		View::renderTemplate('Settings/view.html', [
			'settings' => "view"
		]);
	}
	
	/**
	 * Handle request for income categories - ajax 
	 *
	 * @return json
	 */
	public function loadIncomeCategoriesAction(){
		
		$incomeCategories = IncomeCategory::getUserIncomeCategories($_SESSION['userID']);
		
		echo json_encode($incomeCategories);
	}
	
	/**
	 * Handle request for expense categories - ajax 
	 *
	 * @return json
	 */
	public function loadExpenseCategoriesAction(){
		
		$expenseCategories = ExpenseCategory::getUserExpenseCategories($_SESSION['userID']);
		
		echo json_encode($expenseCategories);
	}
	
	/**
	 * Handle request for payment methods - ajax 
	 *
	 * @return json
	 */
	public function loadPaymentMethodsAction(){
		
		$paymentMethods = PaymentMethod::getUserPaymentMethods($_SESSION['userID']);
		
		echo json_encode($paymentMethods);
	}
	
	/**
	 * Handle request for adding income category - ajax 
	 *
	 * @return json
	 */
	public function addIncomeCategoryAction(){
		
		$data = array("user_id" => $_SESSION['userID'], "name" => $_POST['name']);
		
		$incomeCategory = new IncomeCategory($data);
		
		$incomeCategory->save();
		
		echo json_encode($incomeCategory->error);
	}
	
	/**
	 * Handle request for editing income category - ajax 
	 *
	 * @return json
	 */
	public function editIncomeCategoryAction(){
		
		$data = array("id" => $_POST['id'], "user_id" => $_SESSION['userID'], "name" => $_POST['name']);
		
		$incomeCategory = new IncomeCategory($data);
		
		$incomeCategory->update();
		
		echo json_encode($incomeCategory->error);
	}
	
	/**
	 * Handle request for deleting income category - ajax 
	 *
	 * @return json
	 */
	public function deleteIncomeCategoryAction(){
		
		$data = array("id" => $_POST['id'], "user_id" => $_SESSION['userID'], "name" => $_POST['name']);
		
		$incomeCategory = new IncomeCategory($data);
		
		echo json_encode($incomeCategory->delete());
	}
}