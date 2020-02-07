var sumTemp = 0;
var temp;
var chart;

var urlController = "";
var urlAction = "";

$(function(){
	$.datepicker.setDefaults( $.datepicker.regional[ "pl" ] );
	
	$( "#dateStart" ).datepicker({
		dateFormat: "yy-mm-dd",
		beforeShow: function (input, inst) {
			setTimeout(function () {
				$( "#ui-datepicker-div" ).appendTo("#datepickerPosition1");
				inst.dpDiv.css({
					top: 90,
					left: 15
				});
			}, 0);
		}
	});
	
	$( "#dateEnd" ).datepicker({
		dateFormat: "yy-mm-dd",
		beforeShow: function (input, inst) {
			setTimeout(function () {
				$( "#ui-datepicker-div" ).appendTo("#datepickerPosition2");
				inst.dpDiv.css({
					top: 175,
					left: 15
				});
			}, 0);
		}
	});
	
	$( "#transactionDate" ).datepicker({
		dateFormat: "yy-mm-dd",
		beforeShow: function (input, inst) {
			setTimeout(function () {
				$( "#ui-datepicker-div" ).appendTo("#datepickerPosition3");
				inst.dpDiv.css({
					top: 175,
					left: 15
				});
			}, 0);
		}
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

$('#editOrDeleteConfirm').click(userTransactionAjaxRequest);

$('#editOrDeleteModal').on('hidden.bs.modal', function () {
	$("#deleteError").empty();
	
	$("#transactionAmountComment").empty();
	$("#transactionDateComment").empty();
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
	$("#periodStart").html(start);
	$("#periodEnd").html(end);

	loadIncomesByCategories(start, end, function(sumOfIncomes){
		//console.log("Sum of incomes: " + sumOfIncomes);
		if(sumOfIncomes == 0){
			$("#incomesDetailsCard").hide();
		}else{
			$("#incomesDetailsCard").show();
			loadIncomesDetails(start, end);
		}
	});
	
	
	loadExpensesByCategories(start, end, function(sumOfExpenses){
		//console.log("Sum of expenses: " + sumOfExpenses);
		if(sumOfExpenses == 0){
			$("#expensesDetailsCard").hide();
			$("#chartCard").hide();
		}else{
			$("#expensesDetailsCard").show();
			$("#chartCard").show();
			loadExpensesDetails(start, end);
		}
	});
	
}

$(document).ajaxStop(function(){
	viewBalance();
});

/**----------          ---------- INCOMES ----------          ----------**/

function loadIncomesByCategories(start, end, _callback){
	var userData ={'periodStart': start, 'periodEnd': end};
	
        $.ajax({
			url : '/balance/loadIncomesByCategories',
			data : userData,
			type : 'POST',
			dataType : 'text',
		  
			success : function(json) {
				//console.log("Received: " + json);
				var categoriesData = JSON.parse(json);
				var sum = viewIncomesByCategories(categoriesData);
				_callback(sum);
			},
		  
			error : function(xhr, status, error) {
				alert('Przepraszamy, wystąpił problem! (income categories) ' + error);
			},
		});
}

function viewIncomesByCategories(categoriesData){
	$("#incomesGenerally").empty();
	var sumInc = 0;
	
	categoriesData.forEach(function(category){
		$("#incomesGenerally").append('<tr><td>'+category["category"]+'</td><td>'+category["amount"]+'</td></tr>');
		sumInc += Number(category["amount"]);
	});
	
	$("#incomesGenerally").append('<tr class="font-weight-bold"><td scope="row">suma</td><td scope="row" id="sumOfIncomes">'+sumInc.toFixed(2)+'</td></tr>');
	return sumInc;
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
	
	$(".icon-edit").click(loadModalForEdit);
	$(".icon-trash-empty").click(loadModalForDelete);
}

function viewIncomeDetails(income){
	$("#incomesInDetail").append('<tr id="income_'+income["id"]+'"><td id="income'+income["id"]+'Date">'+income["date"]+'</td><td id="income'+income["id"]+'Category">'+income["category"]+'</td><td id="income'+income["id"]+'Comment">'+income["comment"]+'</td><td id="income'+income["id"]+'Amount" class="amount">'+income["amount"]+'</td><td><div id="income'+income["id"]+'CategoryID" hidden>'+income["category_id"]+'</div><i id="income'+income["id"]+'Edit" class="icon-edit" data-toggle="modal" data-target="#editOrDeleteModal"></i><i id="income'+income["id"]+'Delete" class="icon-trash-empty" data-toggle="modal" data-target="#editOrDeleteModal"></i></td></tr>');
}

/**----------          ---------- EXPENSES ----------          ----------**/

function loadExpensesByCategories(start, end, _callback){
	var userData ={'periodStart': start, 'periodEnd': end};
	
	$.ajax({
		url : '/balance/loadExpensesByCategories',
		data : userData,
		type : 'POST',
		dataType : 'text',
	  
		success : function(json) {
			//console.log("Received: " + json);
			var categoriesData = JSON.parse(json);
			var sum = viewExpensesByCategories(categoriesData);
			_callback(sum);
		},
	  
		error : function(xhr, status, error) {
			alert('Przepraszamy, wystąpił problem! (expenses categories) ' + error);
		},
	});
}

function viewExpensesByCategories(categoriesData){
	sumExp = 0;
	temp = [];
	$("#expensesGenerally").empty();
	
	categoriesData.forEach(function(category){
		$("#expensesGenerally").append('<tr><td>'+category["category"]+'</td><td>'+category["amount"]+'</td></tr>');
		sumExp += Number(category["amount"]);
		
		var dataForChart = {
		  "kategoria": category["category"],
		  "kwota": category["amount"],
		};
		temp.push(dataForChart);
	});
	
	$("#expensesGenerally").append('<tr class="font-weight-bold"><td scope="row">suma</td><td scope="row" id="sumOfExpenses">'+sumExp.toFixed(2)+'</td></tr>');
	reloadChart(temp);
	return sumExp;
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
	
	$(".icon-edit").click(loadModalForEdit);
	$(".icon-trash-empty").click(loadModalForDelete);
}

function viewExpenseDetails(expense){
	$("#expensesInDetail").append('<tr id="expense_'+expense["id"]+'"><td id="expense'+expense["id"]+'Date">'+expense["date"]+'</td><td id="expense'+expense["id"]+'Category">'+expense["category"]+'</td><td id="expense'+expense["id"]+'Comment">'+expense["comment"]+'</td><td id="expense'+expense["id"]+'Amount" class="amount">'+expense["amount"]+'</td><td><div id="expense'+expense["id"]+'CategoryID" hidden>'+expense["category_id"]+'</div><div id="expense'+expense["id"]+'MethodID" hidden>'+expense["method_id"]+'</div><i id="expense'+expense["id"]+'Edit" class="icon-edit" data-toggle="modal" data-target="#editOrDeleteModal"></i><i id="expense'+expense["id"]+'Delete" class="icon-trash-empty" data-toggle="modal" data-target="#editOrDeleteModal"></i></td></tr>');
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

/**----------          ---------- EDIT OR DELETE TRANSACTION ----------          ----------**/

function loadModalForEdit(event){
	$("#modalTitle2").html("Edycja");
	$("#transactionAmount").prop('disabled', false);
	$("#transactionDate").prop('disabled', false);
	$("#incomeCategory").prop('disabled', false);
	$("#expenseCategory").prop('disabled', false);
	$(".form-check-input").prop('disabled', false);
	$("#transactionComment").prop('disabled', false);
	
	urlAction = "edit";
	
	name = event.target.id;
	loadDataToModal(name);
}

function loadModalForDelete(){
	$("#modalTitle2").html("Usunąć transakcję?");
	$("#transactionAmount").prop('disabled', true);
	$("#transactionDate").prop('disabled', true);
	$("#incomeCategory").prop('disabled', true);
	$("#expenseCategory").prop('disabled', true);
	$(".form-check-input").prop('disabled', true);
	$("#transactionComment").prop('disabled', true);
	
	urlAction = "delete";
	
	name = event.target.id;
	loadDataToModal(name);
}

function loadDataToModal(name){
	var tempData = "";
	
	name = name.replace("Edit", "");
	name = name.replace("Delete", "");
	
	if( name.search("expense") == -1 ){
		urlController = "income";
		
		$("#incomeOnly").show();
		$("#expenseOnly").hide();
		
		tempData = $("#"+name+'CategoryID').html();
		$("#incomeCategory").val(tempData);
		
	}else{
		urlController = "expense";
		
		$("#incomeOnly").hide();
		$("#expenseOnly").show();
		
		tempData = $("#"+name+'CategoryID').html();
		$("#expenseCategory").val(tempData);
		
		tempData = $("#"+name+'MethodID').html();
		$("#method"+tempData).prop("checked", true);
	}
	
	tempData = $("#"+name+'Amount').html();
	$("#transactionAmount").val(tempData);
	
	tempData = $("#"+name+'Date').html();
	$("#transactionDate").val(tempData);
	
	tempData = $("#"+name+'Comment').html();
	if( tempData == "-" )
		tempData = "";
	$("#transactionComment").val(tempData);
	
	name = name.replace("income", "");
	name = name.replace("expense", "");
	$("#focusedTransactionID").html(name);
}

/**----------          ---------- UNIVERSAL AJAX REQUEST ----------          ----------**/

function userTransactionAjaxRequest(){
	
	var flagSuccess = true;
	
	var data = {};
	data['id'] =  $('#focusedTransactionID').html();
	
	if(urlAction == 'edit'){
		
		data['date'] = $("#transactionDate").val();
		data['comment'] = $("#transactionComment").val();
		
		if (urlController == "income" ){
			data['category'] = $("#incomeCategory").val();
		}else{
			data['category'] = $("#expenseCategory").val();
			data['method'] = $('input[name=method]:checked', '#radioPanel').val();
		}
		
		data['amount'] = $("#transactionAmount").val();
		data['amount'] = data['amount'].replace(',','.');
		var regexp = /^\d+(\.\d{0,2}){0,1}$/;
		if(!regexp.test(data['amount'])){
			flagSuccess = false;
			$("#transactionAmountComment").html("Podaj prawidłową kwotę.");
		}
	}

	if(flagSuccess){
		//alert(data.toSource());
		$.ajax({
			url : '/' + urlController + '/' + urlAction,
			data : data,
			type : 'POST',
			dataType : 'text',
			
			success : function(json) {
				console.log("Received: " + json);
				var response = JSON.parse(json);
				dealWithResponse(response);
			},
		  
			error : function(xhr, status, error) {
				alert('Przepraszamy, wystąpił problem! (universal ajax) ' + error);
			},
		});
	}
}

function dealWithResponse(response){
	
	if(response["success"] == false){
		
		if (typeof response.errorsAmount !== 'undefined'){
			response.errorsAmount.forEach((value, index, arr) => {
				$("#transactionAmountComment").html(value);
			});
		}
		
		if (typeof response.errorsDate !== 'undefined'){
			response.errorsDate.forEach((value, index, arr) => {
				$("#transactionAmountComment").html(value);
			});
		}
	}
	else{
		$("#editOrDeleteModal").modal("hide");
		
		loadBalance( $("#periodStart").html(), $("#periodEnd").html() );
	}
}