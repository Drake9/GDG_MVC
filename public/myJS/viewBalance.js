var sumTemp = 0;
var temp;
var chart;

$(function(){
	$.datepicker.setDefaults( $.datepicker.regional[ "pl" ] );
	
	$( "#dateStart" ).datepicker({
		dateFormat: "yy-mm-dd"
	});
	
	$( "#dateEnd" ).datepicker({
		dateFormat: "yy-mm-dd"
	});
	
	viewCurrentMonthBalance();
});

$("#currentMonth").on("click", function(){
	viewCurrentMonthBalance();
});

$("#lastMonth").on("click", function(){
	viewLastMonthBalance();
});

$("#currentYear").on("click", function(){
	viewCurrentYearBalance();
});

$("#customBalanceConfirm").on("click", function(){
	viewCustomBalance();
});

function viewCurrentMonthBalance(){
	var date = new Date();
	var firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
	var lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);
	
	var dateStartString = convertDateToString(firstDay);
	var dateEndString = convertDateToString(lastDay);
	
	loadBalance(dateStartString, dateEndString);
}

function viewLastMonthBalance(){
	var date = new Date();
	var firstDay = new Date(date.getFullYear(), date.getMonth() - 1, 1);
	var lastDay = new Date(date.getFullYear(), date.getMonth(), 0);
	
	var dateStartString = convertDateToString(firstDay);
	var dateEndString = convertDateToString(lastDay);
	
	loadBalance(dateStartString, dateEndString);
}

function viewCurrentYearBalance(){
	var date = new Date();
	var firstDay = new Date(date.getFullYear(), 0, 1);
	var lastDay = new Date(date.getFullYear(), 11, 31);
	
	var dateStartString = convertDateToString(firstDay);
	var dateEndString = convertDateToString(lastDay);
	
	loadBalance(dateStartString, dateEndString);
}

function convertDateToString(date){
	var dateAsString = date.getFullYear() + '-' + String(date.getMonth() + 1).padStart(2, '0') + '-' + String(date.getDate()).padStart(2, '0');
	return dateAsString;
}

function viewCustomBalance(){
	var firstDay = $("#dateStart").val();
	var lastDay = $("#dateEnd").val();
	var flagOK = true;
	
	if (firstDay == ""){
		$("#dateStartComment").html("Proszę wypełnić pole!");
		flagOK = false;
	}
	else{
		$("#dateStartComment").empty();
	}
	
	if (lastDay == ""){
		$("#dateEndComment").html("Proszę wypełnić pole!");
		flagOK = false;
	}
	else{
		$("#dateEndComment").empty();
	}
	
	if(firstDay >= lastDay){
		$("#bothDatesComment").html("Data końca bilansu musi być późniejsza, niż data początku!");
		flagOK = false;
	}
	else{
		$("#bothDatesComment").empty();
	}
	
	if(flagOK){
		$("#customPeriodModal").modal('hide');
		loadBalance(firstDay, lastDay);
	}
}

function loadBalance(start, end){
	$("#period").html('za okres:&nbsp ' + start + ' - ' + end);
	
	loadIncomesByCategories(start, end);
	loadIncomesDetails(start, end);
	loadExpensesByCategories(start, end);
	loadExpensesDetails(start, end);
}

$(document).ajaxStop(function(){
	viewBalance();
});

/**----------          ---------- INCOMES ----------          ----------**/

function loadIncomesByCategories(start, end){
	var userData ={'periodStart': start, 'periodEnd': end};
	
        $.ajax({
			url : '/balance/loadIncomesByCategories',
			data : userData,
			type : 'POST',
			dataType : 'text',
		  
			success : function(json) {
				//console.log("Received: " + json);
				var categoriesData = JSON.parse(json);
				viewIncomesByCategories(categoriesData);
			},
		  
			error : function(xhr, status, error) {
				alert('Przepraszamy, wystąpił problem! (income categories) ' + error);
			},
		});
}

function viewIncomesByCategories(categoriesData){
	sumTemp = 0;
	$("#incomesGenerally").empty();
	categoriesData.forEach(viewIncomeCategory);
	$("#incomesGenerally").append('<tr class="font-weight-bold"><td scope="row">suma</td><td scope="row" id="sumOfIncomes">'+sumTemp.toFixed(2)+'</td></tr>');
}

function viewIncomeCategory(category){
	$("#incomesGenerally").append('<tr><td>'+category["category"]+'</td><td>'+category["amount"]+'</td></tr>');
	sumTemp += Number(category["amount"]);
}

/*************************/

function loadIncomesDetails(start, end){
	var userData ={'periodStart': start, 'periodEnd': end};
	$.ajax({
		url : '/balance/loadIncomesInDetail',
		data : userData,
		type : 'POST',
		dataType : 'text',
	  
		success : function(json) {
			//console.log("Received: " + json);
			var incomesData = JSON.parse(json);
			viewIncomesDetails(incomesData);
		},
	  
		error : function(xhr, status, error) {
			alert('Przepraszamy, wystąpił problem! (incomes details) ' + error);
		},
	});
}

function viewIncomesDetails(incomesData){
	$("#incomesInDetail").empty();
	incomesData.forEach(viewIncomeDetails);
}

function viewIncomeDetails(income){
	$("#incomesInDetail").append('<tr id="income_'+income["id"]+'"><td>'+income["date"]+'</td><td>'+income["category"]+'</td><td>'+income["comment"]+'</td><td class="amount">'+income["amount"]+'</td></tr>');
}

/**----------          ---------- EXPENSES ----------          ----------**/

function loadExpensesByCategories(start, end){
	var userData ={'periodStart': start, 'periodEnd': end};
	$.ajax({
		url : '/balance/loadExpensesByCategories',
		data : userData,
		type : 'POST',
		dataType : 'text',
	  
		success : function(json) {
			//console.log("Received: " + json);
			var categoriesData = JSON.parse(json);
			viewExpensesByCategories(categoriesData);
		},
	  
		error : function(xhr, status, error) {
			alert('Przepraszamy, wystąpił problem! (expenses categories) ' + error);
		},
	});
}

function viewExpensesByCategories(categoriesData){
	sumTemp = 0;
	temp = [];
	$("#expensesGenerally").empty();
	categoriesData.forEach(viewExpenseCategory);
	$("#expensesGenerally").append('<tr class="font-weight-bold"><td scope="row">suma</td><td scope="row" id="sumOfExpenses">'+sumTemp.toFixed(2)+'</td></tr>');
	reloadChart(temp);
}

function viewExpenseCategory(category){
	$("#expensesGenerally").append('<tr><td>'+category["category"]+'</td><td>'+category["amount"]+'</td></tr>');
	sumTemp += Number(category["amount"]);
	
	var dataForChart = {
	  "kategoria": category["category"],
	  "kwota": category["amount"],
	};
	temp.push(dataForChart);
}

function reloadChart(data){
	// Themes begin
	am4core.useTheme(am4themes_moonrisekingdom);
	am4core.useTheme(am4themes_animated);
	// Themes end

	// Create chart instance
	chart = am4core.create("chartdiv", am4charts.PieChart);

	// Add data
	chart.data = data;

	// Add and configure Series
	var pieSeries = chart.series.push(new am4charts.PieSeries());
	pieSeries.dataFields.value = "kwota";
	pieSeries.dataFields.category = "kategoria";
	pieSeries.slices.template.stroke = am4core.color("#fff");
	//pieSeries.labels.template.stroke = am4core.color("#fff");
	pieSeries.slices.template.strokeWidth = 2;
	pieSeries.slices.template.strokeOpacity = 1;

	// This creates initial animation
	pieSeries.hiddenState.properties.opacity = 1;
	pieSeries.hiddenState.properties.endAngle = -90;
	pieSeries.hiddenState.properties.startAngle = -90;
}

/*************************/

function loadExpensesDetails(start, end){
	var userData ={'periodStart': start, 'periodEnd': end};
	$.ajax({
		url : '/balance/loadExpensesInDetail',
		data : userData,
		type : 'POST',
		dataType : 'text',
	  
		success : function(json) {
			//console.log("Received: " + json);
			var expensesData = JSON.parse(json);
			viewExpensesDetails(expensesData);
		},
	  
		error : function(xhr, status, error) {
			alert('Przepraszamy, wystąpił problem! (expenses details) ' + error);
		},
	});
}

function viewExpensesDetails(expensesData){
	$("#expensesInDetail").empty();
	expensesData.forEach(viewExpenseDetails);
}

function viewExpenseDetails(expense){
	$("#expensesInDetail").append('<tr id="expense_'+expense["id"]+'"><td>'+expense["date"]+'</td><td>'+expense["category"]+'</td><td>'+expense["comment"]+'</td><td class="amount">'+expense["amount"]+'</td></tr>');
}

/**----------          ---------- BALANCE ----------          ----------**/

function viewBalance(){
	var sumOfIncomes = $("#sumOfIncomes").html();
	var sumOfExpenses = $("#sumOfExpenses").html();
	var balance = Number(sumOfIncomes) - Number(sumOfExpenses);
	
	$("#balance").empty();
	$("#balance").append('<tr><td scope="row">przychody</td><td scope="row">'+sumOfIncomes+'</td></tr>');
	$("#balance").append('<tr><td scope="row">wydatki</td><td scope="row">'+sumOfExpenses+'</td></tr>');
	$("#balance").append('<tr><td scope="row"><b>bilans</b></td><td scope="row"><b>'+Number(balance).toFixed(2)+'</b></td></tr>');
	
	if(balance >= 0){
		$("#balanceComment").removeClass("text-danger");
		$("#balanceComment").addClass("text-success");
		$("#balanceComment").html("Gratulacje! Świetnie dysponujesz finansami!");
	}
	else{
		$("#balanceComment").removeClass("text-success");
		$("#balanceComment").addClass("text-danger");
		$("#balanceComment").html("Uważaj! Popadasz w długi!");
	}
}