
var api_key = $.cookie('apiKey');
var categoryId = $.cookie('categoryId');

	/* Version1 getCookie
	var allcookies = document.cookie;
	alert("All Cookies : " + allcookies );
	
	//Get all the cookies pairs in an array
	cookiearray  = allcookies.split(';');
	Key = cookiearray[0].split('=')[0];
	api_key = cookiearray[0].split('=')[1];
	
	categoryId = cookiearray[2].split('=')[1];
	alert("api_key"+api_key);   
	alert("categoryId"+categoryId); 
	
	categoryId = cookiearray[0].split('=')[2];
	alert(categoryId);
	*/

var rootURL = "../api/v1/categories"+"/"+ categoryId +"/lists";
//alert(rootURL);

var currentCategory;
var listID;
var listStatus;

// Retrieve category list when application starts 
findAll();

$('#btnDelete').hide();
$('#btnUpdate').hide();

$('#btnAdd').click(function() {
	console.log('btnAdd');
	if ($('#categoryName').val() != '')
	{
		$('#categoryId').val('');
		addCategory();
		setTimeout("findAll();" , 500);
		
	}
	newCategory();
	return false;
});

$('#btnUpdate').click(function() {
	console.log('btnUpdate');
	if ($('#categoryId').val() != '' && $('#categoryName').val() != '')
	{
		updateCategory();
		findAll();
		newCategory();
	}	
	return false;
});

$('#btnDelete').click(function() {
	console.log('btnDelete');
	deleteCategory();
	findAll();
	newCategory();
	return false;
});


$('#list a').live('click', function() {
	findById($(this).data('identity'));
	$('#btnUpdate').show();
	$('#btnDelete').show();
	$('#btnAdd').show();
});


//$('#checkbox0').live('click', function() {
//	alert(listStatus);
//	markCategory();
//	findAll();
//	return false;
//});
//
//$('#checkbox1').live('click', function() {
//	alert(listStatus);
//    unmarkCategory();
//    findAll();
//    return false;
//});


function newCategory() {
	console.log('newCategory');
	$('#btnUpdate').hide();
	currentCategory = {};
	renderDetails(currentCategory); // Display empty form
	$('#btnDelete').hide();
	$('#btnAdd').show();
}

function findAll() {
	console.log('findAll');
	$.ajax({
		type: 'GET',
		url: rootURL,
		dataType: "json", // data type of response	
		beforeSend: function (xhr) {
            xhr.setRequestHeader("Authorization", api_key);
        },
		success: function (data, textStatus, xhr) {
            console.log(data);
            //alert(data.lists[1].status);
            renderList(data);
        },
		error: function(jqXHR, textStatus, errorThrown){
			alert('findAll error: ' + errorThrown);
		    var responseText = jQuery.parseJSON(jqXHR.responseText);
		    console.log(responseText);
		    alert(JSON.stringify(responseText));
		}
         
	});
}


function findById(id) {
	console.log('findById: ' + id);
	$.ajax({
		type: 'GET',
		url: rootURL + '/' + id,
		dataType: "json",
		beforeSend: function (xhr) {
            xhr.setRequestHeader("Authorization", api_key);
        },
		success: function(data){
			//$('#btnDelete').show();
			console.log('findById success: ' + data.name);
			//console.log(data.status);
			currentCategory = data;
			renderDetails(currentCategory);
		},
		error: function(jqXHR, textStatus, errorThrown){
			alert('findById error: ' + errorThrown);
		    var responseText = jQuery.parseJSON(jqXHR.responseText);
		    console.log(responseText);
		    alert(JSON.stringify(responseText));
		}
	});
}


function addCategory() {
	console.log('addList');
	$.ajax({
		type: 'POST',
		url: rootURL,
		data: formValues(),
		beforeSend: function (xhr) {
            xhr.setRequestHeader("Authorization", api_key);
        },
		success: function(data, textStatus, jqXHR){
			//alert('List created successfully');
			//$('#btnDelete').show();
			$('#categoryId').val(data.id);
		},
		error: function(jqXHR, textStatus, errorThrown){
			alert('addList error: ' + errorThrown);
		    var responseText = jQuery.parseJSON(jqXHR.responseText);
		    console.log(responseText);
		    alert(JSON.stringify(responseText));
		}
	});
}

function updateCategory() {
	console.log('updateList');
	$.ajax({
		type: 'PUT',
		contentType: 'application/json',
		url: rootURL + '/' + $('#categoryId').val(),
		dataType: "json",
		data: formToJSON(),
		beforeSend: function (xhr) {
            xhr.setRequestHeader("Authorization", api_key);
        },
		success: function(data, textStatus, jqXHR){
			//alert('List updated successfully');
		},
		error: function(jqXHR, textStatus, errorThrown){
			alert('updateList error: ' + errorThrown);
		    var responseText = jQuery.parseJSON(jqXHR.responseText);
		    console.log(responseText);
		    alert(JSON.stringify(responseText));
		}
	});
}

function deleteCategory() {
	console.log('deleteList');
	$.ajax({
		type: 'DELETE',
		url: rootURL + '/' + $('#categoryId').val(),
		beforeSend: function (xhr) {
            xhr.setRequestHeader("Authorization", api_key);
        },
		success: function(data, textStatus, jqXHR){
			//alert('List deleted successfully');
		},
		error: function(jqXHR, textStatus, errorThrown){
			alert('deleteList error: ' + errorThrown);
		    var responseText = jQuery.parseJSON(jqXHR.responseText);
		    console.log(responseText);
		    alert(JSON.stringify(responseText));
		}
	});
}





function renderList(data) {
	console.log(data);
	
    //alert(data.lists[1].created_date);
    //alert(data.lists[1].status);

	var list = data == null ? [] : (data.lists instanceof Array ? data.lists : [data.lists]);
	
	//alert(list);
	$('#list li').remove();

	$.each(list, function(index, list) {
          //console.log( index + ": " + list.id );
          //console.log( index + ": " + list.name);
		console.log (index + ": " + list.status);
		
		listStatus=list.status;
        listID=list.id;
        listName=list.name;

        if (list.status == 0)
        	{
        	
			 //$('#list').append('<li class="list-group-item"><div class="checkbox" ><input type="checkbox" id="checkbox0" /><a href="#"data-identity="'+ list.id + '" >'+ list.name+'</a></div></li>');
			 $('#list').append('<li class="list-group-item"><div class="checkbox" ><input type="checkbox" id="'+ list.id +'checkbox" value="'+ list.id +'" /><a href="#"data-identity="'+ list.id + '" >'+ list.name+'</a></div></li>');

			 
			   $('#'+listID+'checkbox').live('click', function() { 
			    	
				   //alert($('#'+listID+'checkbox').val());
				   //alert(list.id);
				   //alert(list.name);
				   
				   function formToJSONMark() {
						return JSON.stringify({ 	
							
							//"name": $('#'+listID+'checkbox').val(),
							//"name": $('#categoryName').val(),
							"name": list.name,
							"status": 1
							});
					}
				   
			    	function markCategory() {
			    		console.log('markList');
			    		//console.log($('#categoryId').val());
			    		console.log($('#'+listID+'checkbox').val());
			    		$.ajax({
			    			type: 'PUT',
			    			contentType: 'application/json',
			    			url: rootURL + '/' + list.id,
			    			//url: rootURL + '/' + $('#categoryId').val(),
			    			//url: rootURL + '/' + $('#'+listID+'checkbox').val(),
			    			dataType: "json",
			    			data: formToJSONMark(),
			    			beforeSend: function (xhr) {
			    	            xhr.setRequestHeader("Authorization", api_key);
			    	        },
			    			success: function(data, textStatus, jqXHR){

			    				console.log(data);
			    				//$('#categoryList').fadeOut();
			    				
			    			    //$('#list').append('<li><a href="#"data-identity="'+ $('#categoryId').val() + '" >'+  data.name.strike( data.name ) +' </a></li>');
			    				//$('#categoryList'+data.name).remove();
			    				//var newTask = '<li>' + '<p>'+data.name.strike( data.name )+'</p>' + '</li>'
			    				//$('#tasklist').append(newTask);
			    				
			    				//alert( data.name +'  mark successfully');
			    			},
			    			error: function(jqXHR, textStatus, errorThrown){
			    				alert('markList error: ' + errorThrown);
			    			    var responseText = jQuery.parseJSON(jqXHR.responseText);
			    			    console.log(responseText);
			    			    alert(JSON.stringify(responseText));
			    			}
			    		});
			    	}
				    markCategory();
				    findAll();
				    return false;
				});
        	}
	    else
			{
	    	
			//$('#list').append('<li class="list-group-item"><div class="checkbox" ><input type="checkbox" id="checkbox1" /><a href="#"data-identity="'+ list.id + '" >'+ list.name.strike(list.name)+'</a></div></li>');   
			$('#list').append('<li class="list-group-item"><div class="checkbox" ><input type="checkbox" id="'+ list.id +'checkbox"  value="'+ list.id +'" /><a href="#"data-identity="'+ list.id + '" >'+ list.name.strike(list.name)+'</a></div></li>'); 
			
			
			
			$('#'+listID+'checkbox').live('click', function() {
                

				//alert($('#'+listID+'checkbox').val());
				//alert(list.id);
				//alert(list.name);
				
				function formToJSONUnMark() {
					return JSON.stringify({ 
						//"name": $('#'+listID+'checkbox').attr("name"),
						//"name": $('#'+listID+'checkbox').val(),
						"name": list.name,
						"status": 0
						});
				}
				function unmarkCategory() {
					console.log('unmarkList');
					$.ajax({
						type: 'PUT',
						contentType: 'application/json',
						url: rootURL + '/' + list.id,
						//url: rootURL + '/' + $('#categoryId').val(),
						//url: rootURL + '/' + $('#'+listID+'checkbox').val(),
						dataType: "json",
						data: formToJSONUnMark(),
						beforeSend: function (xhr) {
				            xhr.setRequestHeader("Authorization", api_key);
				        },
						success: function(data, textStatus, jqXHR){
							console.log(data);
							//alert( data.name +'  unmark successfully');
						},
						error: function(jqXHR, textStatus, errorThrown){
							alert('unmarkList error: ' + errorThrown);
						    var responseText = jQuery.parseJSON(jqXHR.responseText);
						    console.log(responseText);
						    alert(JSON.stringify(responseText));
						}
					});
				}
			    unmarkCategory();
			    findAll();
			    return false;
			});
			
			
			}
	});
	
	
}


function renderDetails(list) {
	$('#categoryId').val(list.id);
	$('#categoryName').val(list.name);
	//$('#categoryStatus').val(list.status);
	//$('#categoryCreatedDate').val(list.created_date);
}

function formToJSON() {
	return JSON.stringify({
		"id": $('#categoryId').val(), 
		"name": $('#categoryName').val(),
		"status": $('#categoryStatus').val()
		//"created_date": $('#categoryCreatedDate').val()
		});
}


function formValues() {
    return {
            id: $('#categoryId').val(),
            list: $('#categoryName').val(),
            //status: $('#categoryStatus').val()
        };
}


