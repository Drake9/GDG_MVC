<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Income;
use \App\Models\Expense;
use \App\Models\IncomeCategory;
use \App\Models\ExpenseCategory;
use \App\Models\PaymentMethod;
use \App\Models\User;

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
			'settings' => "view",
			'user' => Auth::getUser()
		]);
	}
	
	/**----------          ---------- LOADING DATA ----------          ----------**/
	
	/**
	 * Handle request for income categories - ajax 
	 *
	 * @return json
	 */
	public function loadIncomeCategoriesAction(){
		
		$incomeCategories = IncomeCategory::getUsersCategories($_SESSION['userID']);
		
		echo json_encode($incomeCategories);
	}
	
	/**
	 * Handle request for expense categories - ajax 
	 *
	 * @return json
	 */
	public function loadExpenseCategoriesAction(){
		
		$expenseCategories = ExpenseCategory::getUsersCategories($_SESSION['userID']);
		
		echo json_encode($expenseCategories);
	}
	
	/**
	 * Handle request for payment methods - ajax 
	 *
	 * @return json
	 */
	public function loadPaymentMethodsAction(){
		
		$paymentMethods = PaymentMethod::getUsersCategories($_SESSION['userID']);
		
		echo json_encode($paymentMethods);
	}
	
	/**----------          ---------- INCOME CATEGORIES ----------          ----------**/
	
	/**
	 * Handle request for adding income category - ajax 
	 *
	 * @return json
	 */
	public function addIncomeCategoryAction(){
		
		$data = array("user_id" => $_SESSION['userID'], "name" => $_POST['name']);
		
		$incomeCategory = new IncomeCategory($data);
		
		echo json_encode($incomeCategory->save());
	}
	
	/**
	 * Handle request for editing income category - ajax 
	 *
	 * @return json
	 */
	public function editIncomeCategoryAction(){
		
		$data = array("id" => $_POST['id'], "user_id" => $_SESSION['userID'], "name" => $_POST['name']);
		
		$incomeCategory = new IncomeCategory($data);
		
		echo json_encode($incomeCategory->update());
	}
	
	/**
	 * Handle request for deleting income category - ajax 
	 *
	 * @return json
	 */
	public function deleteIncomeCategoryAction(){
		
		$data = array("id" => $_POST['id'], "user_id" => $_SESSION['userID'], "name" => $_POST['name']);
		
		$incomeCategory = new IncomeCategory($data);
		
		if( Income::isCategoryInUse($incomeCategory->id) ){
			$incomeCategory->success = false;
			$incomeCategory->errorName = "Aby usunąć kategorię będącą w użyciu, najpierw usuń lub edytuj transakcje, które ją wykorzystują.";
			echo json_encode($incomeCategory);
		}else{
			echo json_encode($incomeCategory->delete());
		}
	}
	
	/**----------          ---------- EXPENSE CATEGORIES ----------          ----------**/
	
	/**
	 * Handle request for adding expense category - ajax 
	 *
	 * @return json
	 */
	public function addExpenseCategoryAction(){
		
		if( isset($_POST['expenseLimit']) ){
			$data = array("user_id" => $_SESSION['userID'], "name" => $_POST['name'], "amount_limit" => $_POST['expenseLimit']);
		}else{
			$data = array("user_id" => $_SESSION['userID'], "name" => $_POST['name']);
		}
		
		$expenseCategory = new ExpenseCategory($data);
		
		//$expenseCategory->save();
		
		echo json_encode($expenseCategory->save());
	}
	
	/**
	 * Handle request for editing expense category - ajax 
	 *
	 * @return json
	 */
	public function editExpenseCategoryAction(){
		
		if( isset($_POST['expenseLimit']) ){
			$data = array("id" => $_POST['id'], "user_id" => $_SESSION['userID'], "name" => $_POST['name'], "amount_limit" => $_POST['expenseLimit']);
		}else{
			$data = array("id" => $_POST['id'], "user_id" => $_SESSION['userID'], "name" => $_POST['name']);
		}
		
		$expenseCategory = new ExpenseCategory($data);
		
		//$expenseCategory->update();
		
		echo json_encode($expenseCategory->update());
	}
	
	/**
	 * Handle request for deleting expense category - ajax 
	 *
	 * @return json
	 */
	public function deleteExpenseCategoryAction(){
		
		$data = array("id" => $_POST['id'], "user_id" => $_SESSION['userID'], "name" => $_POST['name']);
		
		$expenseCategory = new ExpenseCategory($data);
		
		if( Expense::isCategoryInUse($expenseCategory->id) ){
			$expenseCategory->success = false;
			$expenseCategory->errorName = "Aby usunąć kategorię będącą w użyciu, najpierw usuń lub edytuj transakcje, które ją wykorzystują.";
			echo json_encode($expenseCategory);
		}else{
			echo json_encode($expenseCategory->delete());
		}
	}
	
	/**----------          ---------- PAYMENT METHODS ----------          ----------**/
	
	/**
	 * Handle request for adding payment method - ajax 
	 *
	 * @return json
	 */
	public function addPaymentMethodAction(){
		
		$data = array("user_id" => $_SESSION['userID'], "name" => $_POST['name']);
		
		$paymentMethod = new PaymentMethod($data);
		
		echo json_encode($paymentMethod->save());
	}
	
	/**
	 * Handle request for editing payment method - ajax 
	 *
	 * @return json
	 */
	public function editPaymentMethodAction(){
		
		$data = array("id" => $_POST['id'], "user_id" => $_SESSION['userID'], "name" => $_POST['name']);
		
		$paymentMethod = new PaymentMethod($data);
		
		echo json_encode($paymentMethod->update());
	}
	
	/**
	 * Handle request for deleting payment method - ajax 
	 *
	 * @return json
	 */
	public function deletePaymentMethodAction(){
		
		$data = array("id" => $_POST['id'], "user_id" => $_SESSION['userID'], "name" => $_POST['name']);
		
		$paymentMethod = new PaymentMethod($data);
		
		if( Expense::isMethodInUse($paymentMethod->id) ){
			$paymentMethod->success = false;
			$paymentMethod->errorName = "Aby usunąć kategorię będącą w użyciu, najpierw usuń lub edytuj transakcje, które ją wykorzystują.";
			echo json_encode($paymentMethod);
		}else{
			echo json_encode($paymentMethod->delete());
		}
	}
	
	/**----------          ---------- USER PROFILE ----------          ----------**/
		
	/**
	 * Handle request for editing user profile - ajax 
	 *
	 * @return json
	 */
	public function updateProfileAction(){
		
		$user = new User($_POST);
		$user->id = $_SESSION['userID'];
		
		echo json_encode($user->updateProfile());
	}
}