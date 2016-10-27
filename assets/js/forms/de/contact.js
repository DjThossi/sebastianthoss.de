var ContactForm = function () {

    return {
        
        //Contact Form
        initContactForm: function () {
	        // Validation
	        $("#sky-form3").validate({
	            // Rules for form validation
	            rules:
	            {
	                name:
	                {
	                    required: true
	                },
	                email:
	                {
	                    required: true,
	                    email: true
	                },
	                message:
	                {
	                    required: true,
	                    minlength: 10
	                }
	            },
	                                
	            // Messages for form validation
	            messages:
	            {
	                name:
	                {
	                    required: 'Bitte gib Deinen Namen an.'
	                },
	                email:
	                {
	                    required: 'Bitte gib Deine E-Mail Adresse an.',
	                    email: 'Bitte gib eine g&uuml;ltige E-Mail Adresse ein.'
	                },
	                message:
	                {
	                    required: 'Eine Nachricht muss sein.',
						minlength: 'Deine Nachricht muss mindestens {0} Zeichen lang sein.'
	                }
	            },
	                                
	            // Do not change code below
	            errorPlacement: function(error, element)
	            {
	                error.insertAfter(element.parent());
	            }
	        });
        }

    };
    
}();