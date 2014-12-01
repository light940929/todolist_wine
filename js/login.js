 var api_key;
 
 var user_id; 	

$('#btnLogin').click(function() {
	
    if($('#email').val()=="" && $('#password').val()==""){
    	alert('Please enter your email & password');
        return false;
    }
    if($('#email').val()==""){
    	alert('Please enter your email');
        $('#email').focus();
        return false;
    }else if($('#password').val()==""){
    	alert('Please enter your password');
        $('#password').focus();
        return false;
    }
	//console.log($("#post_login").validate());
	
	if ($('#email').val() != '' && $('#password').val() != "" ){
		loginUser();
	}
	
});

$('#btnLogout').click(function() {

       logoutUser();
});


function loginUser() {
	
	var rootURLogin = "api/v1/login";
    var formValues = {
            email: $('#email').val(),
            password: $('#password').val()
        };
	
	console.log('Loggin in... ');
	
	$.ajax({
		type: 'POST',
		url: rootURLogin,
		data: formValues,
		success: function(data, textStatus, jqXHR){
			
			//alert(data.name+' login successfully');			
			api_key = data.apiKey;
			user_id = data.id;

			$.cookie('apiKey', api_key, { secure: true});
			$.cookie('userId', user_id, { secure: true});
			
			//$.cookie('apiKey', api_key);
			//$.cookie('userId', user_id);
			
			//document.cookie = "apiKey="+api_key;
			//document.cookie = "categoryId=0;";
			//document.cookie = "userId="+user_id;
			
			window.location = 'views/category.html';
			
			//alert(document.cookie)
			
			/* Version1 localStorage
			localStorage.setItem('apiKey',api_key);
			alert(localStorage.getItem('apiKey'));
			*/
			
			

		},
		error: function(jqXHR, textStatus, errorThrown){
			alert('loginUser error: ' + errorThrown);
		    var responseText = jQuery.parseJSON(jqXHR.responseText);
		    console.log(responseText);
		    alert(JSON.stringify(responseText));
		    //alert(responseText.toSource()); only support for firefox
		    //Object {error: true, message: "Login failed. Incorrect credentials"} 
		}
	});
}



function logoutUser() {
	
	var rootURLogin = "../api/v1/login";
	
	//var allcookies = document.cookie;

	
	//alert("All Cookies : " + allcookies );
	var api_key = $.cookie('apiKey');
	var user_id = $.cookie('userId');
	
	/* Version1 cookie
	cookiearray  = allcookies.split(';');
    api_key = cookiearray[0].split('=')[1];
    user_id = cookiearray[1].split('=')[1];
	alert(user_id);
	*/
		
	console.log('Logout... ');
	
	$.ajax({
		type: 'Delete',
		url: rootURLogin+'/'+ user_id,
		beforeSend: function (xhr) {
            xhr.setRequestHeader("Authorization", api_key);
        },
		success: function(data, textStatus, jqXHR){
				
			$.removeCookie('apiKey');
			$.removeCookie('categoryId');
			$.removeCookie('userId');
             
			 //window.location.replace = 'logout.html';
			 //alert('Logout successfully');
			 window.location = '../index.php';
            
			
			/*  Version1 deleteCookie
			function delete_cookie(name) {
				  document.cookie = name + "=;";
				}
			var cookie_apiKey = 'apiKey';
			delete_cookie(cookie_apiKey);
			
			var cookie_categoryID = 'categoryId';
			delete_cookie(cookie_categoryID);
			
			var cookie_userID = 'userId';
			delete_cookie(cookie_userID);
		    */
			 
		},
		error: function(jqXHR, textStatus, errorThrown){
			alert('Logout error: ' + errorThrown);
		    var responseText = jQuery.parseJSON(jqXHR.responseText);
		    console.log(responseText);
		    alert(JSON.stringify(responseText));
		    
		    $.removeCookie('apiKey');
			$.removeCookie('categoryId');
			$.removeCookie('userId');
			window.location = '../index.php';
		}
	});
}

window.history.forward(1);

/*{
    "error": false,
    "id": 45,
    "name": "air",
    "email": "air@gmail.com",
    "apiKey": "78143ae0f3198379d0203a18dcc06b68",
    "createdAt": "2014-11-03 13:23:46"
}*/	
	
