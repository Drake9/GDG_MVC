var urlItem = "";
var urlAction = "";
var itemID = null;

$('#collapseIncomeCategories').click(function(){
	urlItem = 'IncomeCategory';
	$("#limitParameters").hide();
})

$('#collapseExpenseCategories').click(function(){
	urlItem = 'ExpenseCategory';
	$("#limitParameters").show();
})

$('#collapsePaymentMethods').click(function(){
	urlItem = 'PaymentMethod';
	$("#limitParameters").hide();
})

$('#confirm1').click(userCategoriesAjaxRequest);
$('#confirmUserDataChange').click(userDataAjaxRequest);

$('#universalModal').on('hidden.bs.modal', function () {
	$("#nameComment").empty();
	$("#amountComment").empty();
	
	$('#amountLimitCheck').prop('checked', false);
	$('#expenseLimit').prop('disabled', true);
	$("#expenseLimit").val("");
});

$('#confirmDeleteModal').on('hidden.bs.modal', function () {
	$("#deleteError").empty();
});

$('#userDataModal').on('show.bs.modal', function () {
	$("#inputLogin").val($("#userDataLogin").html());
	$("#inputEmail").val($("#userDataEmail").html());
});

$('#userDataModal').on('hidden.bs.modal', function () {
	$("#loginComment").empty();
	$("#emailComment").empty();
	$("#passwordComment1").empty();
	$("#passwordComment2").empty();
	$("#passwordCommentOld").empty();
});

$('#amountLimitCheck').on('click', function () {

	if( $('#amountLimitCheck').prop('checked') ){
		
		$('#expenseLimit').prop('disabled', false);
		//$('#expenseLimit').prop('required', true);
	}else{
		$('#expenseLimit').prop('disabled', true);
		//$('#expenseLimit').prop('required', false);
		$('#expenseLimit').val("");
	}
});

function loadModalForAdding(){
	$("#modalTitle").html("Dodaj");
	$("#categoryLabel").html("Nazwa kategorii");
	if(urlItem == 'ExpenseCategory'){
		$("#limitParameters").show();
	}
	
	$("#categoryName").val("");
	$('#categoryName').prop('disabled', false);
	//$("#categoryName").css({'-webkit-box-shadow' : 'none', '-moz-box-shadow' : 'none', 'box-shadow' : 'none'});
	
	urlAction = 'add';
	itemID = null;
}

function loadModalForEditing(event){
	$("#modalTitle").html("Edycja");
	$("#categoryLabel").html("Nazwa kategorii");
	if(urlItem == 'ExpenseCategory'){
		$("#limitParameters").show();
	}
	
	name = event.target.id;
	name = name.replace("Edit", "");
	value = $("#"+name).html();
	$("#categoryName").val(value);
	$('#categoryName').prop('disabled', false);
	
	urlAction = 'edit';
	//itemID = name.replace("incomeCategory", "");
	itemID = name.replace(/\D/g,'');
	
	if(urlItem == "ExpenseCategory" && $("#"+name+"Limit").html() != "-"){
		$('#amountLimitCheck').prop('checked', true);
		$('#expenseLimit').prop('disabled', false);
		$("#expenseLimit").val($("#"+name+"Limit").html());
	}
}

function loadModalForDeleting(event){
	$("#modalTitle").html("Potwierdź");
	$("#categoryLabel").html("Czy naprawdę chcesz usunąć podaną kategorię?");
	$("#limitParameters").hide();
	
	name = event.target.id;
	name = name.replace("Delete", "");
	value = $("#"+name).html();
	//console.log(value);
	$("#categoryName").val(value);
	$('#categoryName').prop('disabled', true);
	
	urlAction = 'delete';
	itemID = name.replace(/\D/g,'');
}

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
	
	$("#incomeCategories").append('<tr><td><button id="incomeCategoryNew" class="btn btn-primary btn-sm categoryAdd" data-toggle="modal" data-target="#universalModal">Dodaj nową</button></td><td></td><td></td></tr>');
	
	$(".categoryAdd").click(loadModalForAdding);
	$(".categoryEdit").click(loadModalForEditing);
	$(".categoryDelete").click(loadModalForDeleting);
	
	/*
	$("#incomeCategoryNew").click(function(){
		$("#modalTitle").html("Dodaj");
		$("#categoryName").val("");
		$("#categoryName").css({'-webkit-box-shadow' : 'none', '-moz-box-shadow' : 'none', 'box-shadow' : 'none'});
		urlAction = 'add';
		itemID = null;
	});
	*/
}

function viewIncomeCategory(category){
	$("#incomeCategories").append('<tr><td id="incomeCategory'+category["id"]+'">'+category["name"]+'</td><td><button id="incomeCategoryEdit'+category["id"]+'" class="btn btn-info btn-sm categoryEdit" data-toggle="modal" data-target="#universalModal">Edytuj</button></td><td><button id="incomeCategoryDelete'+category["id"]+'" class="btn btn-danger btn-sm categoryDelete" data-toggle="modal" data-target="#universalModal">Usuń</button></td></tr>');
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
	
	$("#expenseCategories").append('<tr><td><button id="expenseCategoryNew" class="btn btn-primary btn-sm categoryAdd" data-toggle="modal" data-target="#universalModal">Dodaj nową</button></td><td></td><td></td><td></td></tr>');
	
	$(".categoryAdd").click(loadModalForAdding);
	$(".categoryEdit").click(loadModalForEditing);
	$(".categoryDelete").click(loadModalForDeleting);
	
	/*
	$("#expenseCategoryNew").click(function(){
		$("#categoryName").val("");
	});
	*/
}

function viewExpenseCategory(category){
	if(category["amount_limit"] == null)
		category["amount_limit"] = "-";
	
	$("#expenseCategories").append('<tr><td id="expenseCategory'+category["id"]+'">'+category["name"]+'</td><td id="expenseCategory'+category["id"]+'Limit">'+category["amount_limit"]+'</td><td><button id="expenseCategoryEdit'+category["id"]+'" class="btn btn-info btn-sm categoryEdit" data-toggle="modal" data-target="#universalModal">Edytuj</button></td><td><button id="expenseCategoryDelete'+category["id"]+'" class="btn btn-danger btn-sm categoryDelete" data-toggle="modal" data-target="#universalModal">Usuń</button></td></tr>');
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
	$("#paymentMethods").append('<tr><td><button id="paymentMethodNew" class="btn btn-primary btn-sm categoryAdd" data-toggle="modal" data-target="#universalModal">Dodaj nową</button></td><td></td><td></td></tr>');
	
	$(".categoryAdd").click(loadModalForAdding);
	$(".categoryEdit").click(loadModalForEditing);
	$(".categoryDelete").click(loadModalForDeleting);
	
	/*
	$("#paymentMethodNew").click(function(){
		$("#categoryName").val("");
	});
	*/
}

function viewPaymentMethod(method){
	$("#paymentMethods").append('<tr><td id="paymentMethod'+method["id"]+'">'+method["name"]+'</td><td><button id="paymentMethodEdit'+method["id"]+'" class="btn btn-info btn-sm categoryEdit" data-toggle="modal" data-target="#universalModal">Edytuj</button></td><td><button id="paymentMethodDelete'+method["id"]+'" class="btn btn-danger btn-sm categoryDelete" data-toggle="modal" data-target="#universalModal">Usuń</button></td></tr>');
}
/*
function loadNameToModal(event){
	name = event.target.id;
	name = name.replace("Edit", "");
	value = $("#"+name).html();
	$("#categoryName").val(value);
}*/

/**----------          ---------- USER CATEGORIES AJAX REQUEST ----------          ----------**/

function userCategoriesAjaxRequest(){
	
	var data = {};
	data['name'] =  $('#categoryName').val();
	
	if(itemID){
		data['id'] = itemID;
	}
	
	var flag = true;
	
	if( urlItem == 'ExpenseCategory' && $('#amountLimitCheck').prop('checked') ){
		var regexp = /^\d+(\.\d{0,2}){0,1}$/;
		data['expenseLimit'] = $('#expenseLimit').val();
		data['expenseLimit'] = data['expenseLimit'].replace(',','.');
		
		if(!regexp.test(data['expenseLimit'])){
			flag = false;
			$("#amountComment").html("Podaj prawidłową kwotę.");
		}
			
	}

	if(flag){
		$.ajax({
			url : '/settings/' + urlAction + urlItem,
			data : data,
			type : 'POST',
			dataType : 'text',
			
			success : function(json) {
				//console.log("Received: " + json);
				var response = JSON.parse(json);
				dealWithResponseCategories(response);
			},
		  
			error : function(xhr, status, error) {
				alert('Przepraszamy, wystąpił problem! (universal ajax) ' + error);
			},
		});
	}
}

function dealWithResponseCategories(response){
	
	if(response["success"] == false){
		$("#nameComment").html(response["errorName"]);
		$("#amountComment").html(response["errorAmountLimit"]);
	}
	else{
		$("#universalModal").modal("hide");
		
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

/**----------          ---------- USER DATA AJAX REQUEST ----------          ----------**/

function userDataAjaxRequest(){
	
	var data = {};
	data['login'] =  $('#inputLogin').val();
	data['email'] =  $('#inputEmail').val();
	data['password1'] =  $('#inputPassword1').val();
	data['password2'] =  $('#inputPassword2').val();
	data['passwordOld'] =  $('#inputPasswordOld').val();
	
	var flag1 = validateLogin(data['login']);
	var flag2 = validateEmail(data['email']);
	var flag3 = validatePasswords(data['password1'], data['password2']);
	var flag4 = validatePasswordOld(data['passwordOld']);

	if( flag1 && flag2 && flag3 && flag4 ){
		$.ajax({
			url : '/settings/updateProfile',
			data : data,
			type : 'POST',
			dataType : 'text',
			
			success : function(json) {
				console.log("Received: " + json);
				var response = JSON.parse(json);
				dealWithResponseUserData(response);
			},
		  
			error : function(xhr, status, error) {
				alert('Przepraszamy, wystąpił problem! (user data ajax) ' + error);
			},
		});
	}
}

function validateLogin(login){
	
	if(login == ""){
		$("#loginComment").html("Login jest wymagany.");
		return false;
	}else if(login.length < 3 || login.length > 20){
		$("#loginComment").html("Login musi posiadać od 3 do 20 znaków!");
		return false;
	}else{
		var regexp = /^[0-9a-zA-Z_.-]+$/;
		if(!regexp.test(login)){
			$("#loginComment").html("Login może składać się tylko z liter i cyfr (bez polskich znaków)");
			return false;
		}
	}
	
	$("#loginComment").empty();
	return true;
}

function validateEmail(email){
	
	if(email == ""){
		$("#emailComment").html("Adres e-mail jest wymagany.");
		return false;
	}else{
		var regexp = /^[0-9a-z_.-]+@[0-9a-z.-]+\.[a-z]{2,3}$/i;
		if(!regexp.test(email)){
			$("#emailComment").html("Podaj poprawny adres e-mail!");
			return false;
		}
	}
	
	$("#emailComment").empty();
	return true;
}

function validatePasswords(pass1, pass2){
	
	if(pass1 != "" || pass2 != ""){
		if(pass1 != pass2){
			$("#passwordComment1").html("Podane hasła nie są identyczne!");
			return false;
		}else if(pass1.lenght < 8 || pass1.lenght > 20){
			$("#passwordComment1").html("Hasło musi posiadać od 8 do 30 znaków!");
			return false;
		}else{
			var regexp = /.*[a-z]+.*/i;
			
			if(!regexp.test(pass1)){
				$("#passwordComment1").html("Hasło musi zawierać przynajmniej jedną literę.");
				return false;
			}
			
			regexp = /.*\d+.*/i;
			
			if(!regexp.test(pass1)){
				$("#passwordComment1").html("Hasło musi zawierać przynajmniej jedną cyfrę.");
				return false;
			}
			
			$("#passwordComment1").empty();
			return true;
		}
	}else{
		$("#passwordComment1").empty();
		return true;
	}
}

function validatePasswordOld(pass){
	
	if(pass == ""){
		$("#passwordCommentOld").html("To pole jest wymagane.");
		return false;
	}else{
		$("#passwordCommentOld").empty();
		return true;
	}
}

function dealWithResponseUserData(response){
	
	if(response["success"] == false){
		$("#loginComment").html(response["errorsLogin"]);
		$("#emailComment").html(response["errorsEmail"]);
		$("#passwordComment1").html(response["errorsPassword"]);
		$("#passwordCommentOld").html(response["errorPasswordOld"]);
	}
	else{
		$("#userDataModal").modal("hide");
		$("#userDataLogin").html(response["login"]);
		$("#userDataEmail").html(response["email"]);
	}
}