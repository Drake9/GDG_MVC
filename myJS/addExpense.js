$("#amount").change( function(){
	validateAmountLimit();
});

$("#category").change( function(){
	validateAmountLimit();
});

$("#date").change( function(){
	validateAmountLimit();
});

function validateAmountLimit(){
	let currentDate = new Date();
	let userDate = new Date($("#date").val());
	let amount = $("#amount").val();
	
	if( amount != "" && $("#category").val() ){
		
		let amountLimit = Number($("#categoryLimit" + $("#category").val()).html());
		
		if( amountLimit != "" ){
		
			if( userDate.getFullYear() == currentDate.getFullYear() && userDate.getMonth() == currentDate.getMonth() ){

				if( amountLimit < Number($("#amount").val()) ){
					$("#errorAmount").html("Pozostały limit na wydatki z tej kategorii w tym miesiącu wynosi: " + amountLimit + " zł!");
				}
				else{
					$("#errorAmount").html("Wydatek nie przekroczy limitu. Pozostanie: " + (amountLimit - Number($("#amount").val())) + " zł.");
				}
				
			}else{
				requestDifferentMonthLimit(amount, $("#date").val(), $("#category").val());
			}
				
		}else{
			$("#errorAmount").empty();
		}
			
	}else{
		$("#errorAmount").empty();
	}
}

function requestDifferentMonthLimit(amount, date, category){
	
	let data = {};
	data['amount'] =  amount;
	data['date'] =  date;
	data['category'] =  category;
	
	$.ajax({
		url : '/expense/getLimit',
		data : data,
		type : 'POST',
		dataType : 'text',
		
		success : function(json) {
			console.log("Received: " + json);
			let response = JSON.parse(json);
			dealWithResponse(response);
		},
	  
		error : function(xhr, status, error) {
			console.log('Wystąpił problem! (ajax) ' + error);
		},
	});

}

function dealWithResponse(response){
	
	if( response != "null"){
		let amountLimit = Number(response);
		
		if( amountLimit < Number($("#amount").val()) ){
			$("#errorAmount").html("Pozostały limit na wydatki z tej kategorii w tym miesiącu wynosi: " + amountLimit + " zł!");
		}
		else{
			$("#errorAmount").html("Wydatek nie przekroczy limitu. Pozostanie: " + (amountLimit - Number($("#amount").val())) + " zł.");
		}
	}
}