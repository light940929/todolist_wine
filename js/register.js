var rootURLregister = "../api/v1/register";

$('#btnRegister').click(function() {
	
	 if($('#name').val()=="" && $('#email').val()=="" && $('#password').val()==""){
	    	alert('Please enter your name & email & password')
	        return false;
	    }
	    if($('#email').val()==""){
	    	alert('Please enter your email')
	        $('#email').focus();
	        return false;
	    }else if($('#password').val()==""){
	    	alert('Please enter your password')
	        $('#password').focus();
	        return false;
	    }else if($('#name').val()==""){
	    	alert('Please enter your name')
	        $('#name').focus();
	        return false;
	    }
	    
	
	if ($('#name').val() != '' && $('#email').val() != '' && $('#password').val() != "")
		registerUser();
	return false;
	
});


function registerUser() {
	
    var formValues = {
    		name: $('#name').val(),
            email: $('#email').val(),
            password: $('#password').val()
        };
	
	console.log('Register... ');
	
	$.ajax({
		type: 'POST',
		url: rootURLregister,
		data: formValues,
		success: function(data, textStatus, jqXHR){
			//alert(' register successfully');			

			window.location = '../index.php';

		},
		error: function(jqXHR, textStatus, errorThrown){
			alert('register error: ' + errorThrown);
		    var responseText = jQuery.parseJSON(jqXHR.responseText);
		    console.log(responseText);
		    alert(JSON.stringify(responseText));
		}
	});
}
