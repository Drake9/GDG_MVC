var today = new Date();

$(function(){
	$.datepicker.setDefaults( $.datepicker.regional[ "pl" ] );
	$( "#date" ).datepicker({
		dateFormat: "yy-mm-dd"
	});
	
	if( $('#date').val() == "" ){
		var dd = String(today.getDate()).padStart(2, '0');
		var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
		var yyyy = today.getFullYear();

		var todayString = yyyy + '-' + mm + '-' + dd;

		$('#date').attr("value", todayString);
	}
});

/*
$(document).ready(function(){
	$.datepicker.setDefaults( $.datepicker.regional[ "pl" ] );
	$("#date").datepicker({
		dateFormat: "yy-mm-dd"
	});
	
	var dd = String(today.getDate()).padStart(2, '0');
	var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
	var yyyy = today.getFullYear();

	var todayString = yyyy + '-' + mm + '-' + dd;

	$('#date').attr("value", todayString);
});
*/