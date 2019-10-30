var urlItem = "";
var urlAction = "";
var itemID = null;

$('#collapseIncomeCategories').click(function(){
	urlItem = 'IncomeCategory';
})

$('#collapseExpenseCategories').click(function(){
	urlItem = 'ExpenseCategory';
})

$('#collapsePaymentMethods').click(function(){
	urlItem = 'PaymentMethod';
})

$('#confirm1').click(addOrEditRequest)
$('#confirmDelete').click(deleteCategory)

$('#addOrEditModal').on('hidden.bs.modal', function () {
	$("#nameComment").empty();
});

$('#confirmDeleteModal').on('hidden.bs.modal', function () {
	$("#deleteError").empty();
});

$(function(){
	loadIncomeCategories();
	loadExpenseCategories();
	loadPaymentMethods();
});

/**----------          ---------- INCOME CATEGORIES ----------          ----------**/

function loadIncomeCategories(){

	$.ajax({
		url : '/settings/loadIncomeCategories',
	  
		success : function(json) {
			//console.log("Received: " + json);
			var incomeCategories = JSON.parse(json);
			viewIncomeCategories(incomeCategories);
		},
	  
		error : function(xhr, status, error) {
			alert('Przepraszamy, wystąpił problem! (income categories) ' + error);
		},
	});
}

function viewIncomeCategories(incomeCategories){
	$("#incomeCategories").empty();
	incomeCategories.forEach(viewIncomeCategory);
	$(".incomeCategoryEdit").click(loadNameToModalEdit);
	$(".incomeCategoryDelete").click(loadNameToModalDelete);
	
	$("#incomeCategories").append('<tr><td><button id="incomeCategoryNew" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addOrEditModal">Dodaj nową</button></td><td></td><td></td></tr>');
	
	$("#incomeCategoryNew").click(function(){
		$("#categoryName").val("");
		$("#categoryName").css({'-webkit-box-shadow' : 'none', '-moz-box-shadow' : 'none', 'box-shadow' : 'none'});
		urlAction = 'add';
		itemID = null;
	});
}

function viewIncomeCategory(category){
	$("#incomeCategories").append('<tr><td id="incomeCategory'+category["id"]+'">'+category["name"]+'</td><td><button id="incomeCategoryEdit'+category["id"]+'" class="btn btn-info btn-sm incomeCategoryEdit" data-toggle="modal" data-target="#addOrEditModal">Edytuj</button></td><td><button id="incomeCategoryDelete'+category["id"]+'" class="btn btn-danger btn-sm incomeCategoryDelete" data-toggle="modal" data-target="#confirmDeleteModal">Usuń</button></td></tr>');
}

function loadNameToModalEdit(event){
	name = event.target.id;
	name = name.replace("Edit", "");
	value = $("#"+name).html();
	$("#categoryName").val(value);
	urlAction = 'edit';
	itemID = name.replace("incomeCategory", "");
}

function loadNameToModalDelete(event){
	name = event.target.id;
	name = name.replace("Delete", "");
	value = $("#"+name).html();
	$("#categoryForDelete").val(value);
	//urlAction = 'delete';
	itemID = name.replace("incomeCategory", "");
}

/**----------          ---------- EXPENSE CATEGORIES ----------          ----------**/

function loadExpenseCategories(){

	$.ajax({
		url : '/settings/loadExpenseCategories',
	  
		success : function(json) {
			//console.log("Received: " + json);
			var expenseCategories = JSON.parse(json);
			viewExpenseCategories(expenseCategories);
		},
	  
		error : function(xhr, status, error) {
			alert('Przepraszamy, wystąpił problem! (expense categories) ' + error);
		},
	});
}

function viewExpenseCategories(expenseCategories){
	$("#expenseCategories").empty();
	expenseCategories.forEach(viewExpenseCategory);
	$("#expenseCategories").append('<tr><td><button id="expenseCategoryNew" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addOrEditModal">Dodaj nową</button></td><td></td><td></td></tr>');
	
	$("#expenseCategoryNew").click(function(){
		$("#categoryName").val("");
	});
}

function viewExpenseCategory(category){
	$("#expenseCategories").append('<tr><td id="expenseCategory'+category["id"]+'">'+category["name"]+'</td><td><button id="expenseCategoryEdit'+category["id"]+'" class="btn btn-info btn-sm" data-toggle="modal" data-target="#addOrEditModal">Edytuj</button></td><td><button class="btn btn-danger btn-sm">Usuń</button></td></tr>');
	
	//$("#expenseCategoryEdit"+category["id"]).click(loadNameToModal);
}
/*
function loadNameToModal(event){
	name = event.target.id;
	name = name.replace("Edit", "");
	value = $("#"+name).html();
	$("#categoryName").val(value);
}*/

/**----------          ---------- PAYMENT METHODS ----------          ----------**/

function loadPaymentMethods(){

	$.ajax({
		url : '/settings/loadPaymentMethods',
	  
		success : function(json) {
			//console.log("Received: " + json);
			var paymentMethods = JSON.parse(json);
			viewPaymentMethods(paymentMethods);
		},
	  
		error : function(xhr, status, error) {
			alert('Przepraszamy, wystąpił problem! (payment methods) ' + error);
		},
	});
}

function viewPaymentMethods(paymentMethods){
	$("#paymentMethods").empty();
	paymentMethods.forEach(viewPaymentMethod);
	$("#paymentMethods").append('<tr><td><button id="paymentMethodNew" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editModal">Dodaj nową</button></td><td></td><td></td></tr>');
	
	$("#paymentMethodNew").click(function(){
		$("#categoryName").val("");
	});
}

function viewPaymentMethod(method){
	$("#paymentMethods").append('<tr><td id="paymentMethod'+method["id"]+'">'+method["name"]+'</td><td><button id="paymentMethodEdit'+method["id"]+'" class="btn btn-info btn-sm" data-toggle="modal" data-target="#editModal">Edytuj</button></td><td><button class="btn btn-danger btn-sm">Usuń</button></td></tr>');
	
	//$("#expenseCategoryEdit"+category["id"]).click(loadNameToModal);
}
/*
function loadNameToModal(event){
	name = event.target.id;
	name = name.replace("Edit", "");
	value = $("#"+name).html();
	$("#categoryName").val(value);
}*/

/**----------          ---------- UNIVERSAL AJAX REQUEST ----------          ----------**/

function addOrEditRequest(){
	
	if(itemID){
		var data = {'id': itemID, 'name': $('#categoryName').val()};
	}
	else{
		var data = {'name': $('#categoryName').val()};
	}

	$.ajax({
		url : '/settings/' + urlAction + urlItem,
		data : data,
		type : 'POST',
		dataType : 'text',
		
		success : function(json) {
			//console.log("Received: " + json);
			var response = JSON.parse(json);
			dealWithResponse(response);
		},
	  
		error : function(xhr, status, error) {
			alert('Przepraszamy, wystąpił problem! (universal ajax) ' + error);
		},
	});
}

function dealWithResponse(response){
	
	if(response != null){
		$("#nameComment").html(response);
	}
	else{
		$("#addOrEditModal").modal("hide");
		
		if( urlItem == "IncomeCategory"){
			loadIncomeCategories();
		}
		else if( urlItem == "ExpenseCategory"){
			loadExpenseCategories();
		}
		else{
			loadPaymentMethods();
		}
	}
}

/**----------          ---------- DELETE AJAX REQUEST ----------          ----------**/

function deleteCategory(){
	
	var data = {'id': itemID, 'name': $('#categoryForDelete').val()};

	$.ajax({
		url : '/settings/delete' + urlItem,
		data : data,
		type : 'POST',
		dataType : 'text',
		
		success : function(json) {
			console.log("Received: " + json);
			var response = JSON.parse(json);
			dealWithDeleteResponse(response);
			
		},
	  
		error : function(xhr, status, error) {
			alert('Przepraszamy, wystąpił problem! (delete ajax) ' + error);
		},
	});
}

function dealWithDeleteResponse(response){
	
	if(response != true){
		$("#deleteError").html(response);
	}
	else{
		$("#confirmDeleteModal").modal("hide");
		
		if( urlItem == "IncomeCategory"){
			loadIncomeCategories();
		}
		else if( urlItem == "ExpenseCategory"){
			loadExpenseCategories();
		}
		else{
			loadPaymentMethods();
		}
	}
}