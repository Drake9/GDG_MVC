<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Balance as BalanceModel;

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
		View::renderTemplate('/Balance/view.html', [
			'balance' => "view"
		]);
	}
	
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
}