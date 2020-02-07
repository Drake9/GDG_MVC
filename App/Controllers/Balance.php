<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Balance as BalanceModel;
use \App\Models\IncomeCategory;
use \App\Models\ExpenseCategory;
use \App\Models\PaymentMethod;

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
		$incomeCategories = IncomeCategory::getUserIncomeCategories($_SESSION['userID']);
		$expenseCategories = ExpenseCategory::getUserexpenseCategories($_SESSION['userID']);
		$paymentMethods = PaymentMethod::getUserPaymentMethods($_SESSION['userID']);
		
		View::renderTemplate('/Balance/view.html', [
			'balance' => "view",
			'income_categories' => $incomeCategories,
			'expense_categories' => $expenseCategories,
			'payment_methods' => $paymentMethods
		]);
	}
	
	/**----------          ---------- LOADING BALANCE DATA ----------          ----------**/
	
	/**
	 * Handle request for incomes summed up by categories - ajax 
	 *
	 * @return json
	 */
	public function loadIncomesByCategoriesAction(){
		$balance = new BalanceModel($_SESSION['userID'], $_POST['periodStart'], $_POST['periodEnd']);
		
		$data = $balance->getIncomesByCategories();
		
		echo json_encode($data);
	}
	
	/**
	 * Handle request for expenses summed up by categories - ajax 
	 *
	 * @return json
	 */
	public function loadExpensesByCategoriesAction(){
		$balance = new BalanceModel($_SESSION['userID'], $_POST['periodStart'], $_POST['periodEnd']);
		
		$data = $balance->getExpensesByCategories();
		
		echo json_encode($data);
	}
	
	/**
	 * Handle request for detailed incomes - ajax 
	 *
	 * @return json
	 */
	public function loadIncomesInDetailAction(){
		$balance = new BalanceModel($_SESSION['userID'], $_POST['periodStart'], $_POST['periodEnd']);
		
		$data = $balance->getDetailedIncomes();
		
		echo json_encode($data);
	}
		
	/**
	 * Handle request for detailed expenses - ajax 
	 *
	 * @return json
	 */
	public function loadExpensesInDetailAction(){
		$balance = new BalanceModel($_SESSION['userID'], $_POST['periodStart'], $_POST['periodEnd']);
		
		$data = $balance->getDetailedExpenses();
		
		echo json_encode($data);
	}
	
	/**----------          ---------- LOADING TRANSACTION CATEGORIES - PROBABLY FOR DELETE ----------          ----------**/
	
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
}