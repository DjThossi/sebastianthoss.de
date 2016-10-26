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
	                    required: 'Bitte geben Sie Ihren Namen ein.'
	                },
	                email:
	                {
	                    required: 'Bitte geben Sie Ihre E-Mail Adresse ein.',
	                    email: 'Bitte geben Sie eine g&uuml;ltige E-Mail Adresse ein.'
	                },
	                message:
	                {
	                    required: 'Bitte geben sie eine Nachricht ein.',
						minlength: 'Ihre Nachricht muss mindestens {0} Zeichen lang sein.'
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