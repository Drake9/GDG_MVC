<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\ExpenseCategory;
use \App\Models\PaymentMethod;
use \App\Models\Expense as ExpenseModel;
use \App\Flash;

/**
 * Expense controller
 *
 */
class Expense extends Authenticated{
	
	/**
	 * Adding new expense
	 *
	 * @return void
	 */
	public function newAction(){
		
		$expenseCategories = ExpenseCategory::getUserExpenseCategories($_SESSION['userID']);
		$paymentMethods = PaymentMethod::getUserPaymentMethods($_SESSION['userID']);
		
		View::renderTemplate('Expense/new.html', [
			'expense' => "new",
			'expense_categories' => $expenseCategories,
			'payment_methods' => $paymentMethods
		]);
	}
	
	/**
     * Add new expense
     *
     * @return void
     */
	public function createAction(){
		$expense = new ExpenseModel($_POST);
		$expense->userID = $_SESSION['userID'];
		
		if ($expense->save()){
			Flash::addMessage('Nowy wydatek został dodany pomyślnie.');
			$this->redirect('/expense/new');
		}
		else{
			$expenseCategories = ExpenseCategory::getUserExpenseCategories($_SESSION['userID']);
			$paymentMethods = PaymentMethod::getUserPaymentMethods($_SESSION['userID']);
			Flash::addMessage('Podane dane są nieprawidłowe.', Flash::WARNING);
			View::renderTemplate('Expense/new.html', [
				'expense' => $expense,
				'expense_categories' => $expenseCategories,
				'payment_methods' => $paymentMethods
			]);
		}
	}
	
	/**
     * Edit expense - ajax request
     *
     * @return json
     */
	public function editAction(){
		$expense = new ExpenseModel($_POST);
		$expense->userID = $_SESSION['userID'];
		
		if ($expense->update()){
			$expense->success = true;
		}
		else{
			$expense->success = false;
		}
		
		echo json_encode($expense);
	}
	
	/**
     * Delete expense - ajax request
     *
     * @return json
     */
	public function deleteAction(){
		$expense = new ExpenseModel($_POST);
		$expense->userID = $_SESSION['userID'];
		
		if ($expense->delete()){
			$expense->success = true;
		}
		else{
			$expense->success = false;
		}
		
		echo json_encode($expense);
	}
	
	/**
     * Get this month amount limit - ajax request
     *
     * @return json
     */
	public function getLimitAction(){
		$expense = new ExpenseModel($_POST);
		$expense->userID = $_SESSION['userID'];
		
		$amountLimit = $expense->getAmountLimitLeft();

		echo json_encode($amountLimit);
	}
}