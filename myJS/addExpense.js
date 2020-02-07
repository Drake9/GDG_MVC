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
	var currentDate = new Date();
	var userDate = new Date($("#date").val());
	
	if( $("#amount").val() != "" && $("#category").val() && userDate.getFullYear() == currentDate.getFullYear() && userDate.getMonth() == currentDate.getMonth() ){
		var amountLimit = $("#categoryLimit" + $("#category").val()).html();
		
		if( amountLimit != "" && amountLimit < $("#amount").val() ){
			$("#errorAmount").html("Pozostały limit na wydatki z tej kategorii w tym miesiącu wynosi: "+amountLimit+" zł.");
		}
		else{
			$("#errorAmount").empty();
		}
	}else{
		$("#errorAmount").empty();
	}
}