$.extend( $.validator.messages, {
			required: "To pole jest wymagane.",
			remote: "Proszę o wypełnienie tego pola.",
			email: "Proszę o podanie prawidłowego adresu email.",
			url: "Proszę o podanie prawidłowego URL.",
			date: "Proszę o podanie prawidłowej daty.",
			dateISO: "Proszę o podanie prawidłowej daty (ISO).",
			number: "Proszę o podanie prawidłowej liczby.",
			digits: "Proszę o podanie samych cyfr.",
			creditcard: "Proszę o podanie prawidłowej karty kredytowej.",
			equalTo: "Proszę o podanie tej samej wartości ponownie.",
			extension: "Proszę o podanie wartości z prawidłowym rozszerzeniem.",
			nipPL: "Proszę o podanie prawidłowego numeru NIP.",
			phonePL: "Proszę o podanie prawidłowego numeru telefonu",
			maxlength: $.validator.format( "Proszę o podanie nie więcej niż {0} znaków." ),
			minlength: $.validator.format( "Proszę o podanie przynajmniej {0} znaków." ),
			rangelength: $.validator.format( "Proszę o podanie wartości o długości od {0} do {1} znaków." ),
			range: $.validator.format( "Proszę o podanie wartości z przedziału od {0} do {1}." ),
			max: $.validator.format( "Proszę o podanie wartości mniejszej bądź równej {0}." ),
			min: $.validator.format( "Proszę o podanie wartości większej bądź równej {0}." ),
			pattern: $.validator.format( "Pole zawiera niedozwolone znaki." )
		} );
	
		$.validator.addMethod('validPassword',
			function(value, element, param){
				if(value != ''){
					if(value.match(/.*[a-z]+.*/i) == null){
						return false;
					}
					if(value.match(/.*\d+.*/) == null){
						return false;
					}
				}
				
				return true;
			},
			'Hasło musi zawierać przynajmniej jedną literę i jedną cyfrę.'
		);
	
		$(document).ready(function(){
		
			$('#formSignup').validate({
				lang: 'pl',
				rules: {
					login: {
						required: true,
						remote: '/account/validate-login'
					},
					email: {
						required: true,
						email: true,
						remote: '/account/validate-email'
					},
					password1: {
						required: true,
						minlength: 8,
						validPassword: true
					},
					password2: {
						equalTo: '#inputPassword1'
					}
				},
				messages: {
					login: {
						remote: 'Podany login jest już zajęty.'
					},
					email: {
						remote: 'Istnieje już konto przypisane do podanego adresu e-mail.'
					}
				}
			});
		});