var rootURL = "../api/v1/categories";


var api_key = $.cookie('apiKey');
//console.log($.cookie('apiKey'));
  
var currentCategory;

// Retrieve category list when application starts 
findAll();  //setInterval('findAll()',5000);//refresh on 5 Sec



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

$('#categoryList a').live('click', function() {
	findById($(this).data('identity'));
	$('#btnUpdate').show();
	$('#btnDelete').show();
	$('#btnAdd').show();
	
});


//double click to link list
$('#categoryList a').live('dblclick','li',function(){
	//findByIdList($(this).data('identity'));
	console.log(findByIdList($(this).data('identity')));
	
});


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
            //console.log(data);
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

function findByIdList(id) {
	console.log('findById: ' + id);
	$.ajax({
		type: 'GET',
		url: rootURL + '/' + id + 'lists',
		dataType: "json",
		beforeSend: function (xhr) {
            xhr.setRequestHeader("Authorization", api_key);
        },
		success: function(data){
			//$('#btnDelete').show();
			console.log('findByIdList success: ' + data.id);
			//alert(data.id);
			//document.cookie = "categoryId="+data.id;
			//$.cookie('categoryId', data.id);
			$.cookie('categoryId', data.id , { secure: true});
		
			window.location = 'list.html';
		},
		error: function(jqXHR, textStatus, errorThrown){
			alert('findByIdList error: ' + errorThrown);
		    var responseText = jQuery.parseJSON(jqXHR.responseText);
		    console.log(responseText);
		    alert(JSON.stringify(responseText));
		}
		
	});
}

function addCategory() {
	console.log('addCategory');
	$.ajax({
		type: 'POST',
		url: rootURL,
		data: formValues(),
		beforeSend: function (xhr) {
            xhr.setRequestHeader("Authorization", api_key);
        },
		success: function(data, textStatus, jqXHR){
			//alert('Category created successfully');
			//$('#btnDelete').show();
			$('#categoryId').val(data.id);
		},
		error: function(jqXHR, textStatus, errorThrown){
			alert('addCategory error: ' + errorThrown);
		    var responseText = jQuery.parseJSON(jqXHR.responseText);
		    console.log(responseText);
		    alert(JSON.stringify(responseText));
		}
	});
}

function updateCategory() {
	console.log('updateCategory');
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
			//alert('Category updated successfully');
		},
		error: function(jqXHR, textStatus, errorThrown){
			alert('updateCategory error: ' + errorThrown);
		    var responseText = jQuery.parseJSON(jqXHR.responseText);
		    console.log(responseText);
		    alert(JSON.stringify(responseText));
		}
	});
}

function deleteCategory() {
	console.log('deleteCategory');
	$.ajax({
		type: 'DELETE',
		url: rootURL + '/' + $('#categoryId').val(),
		beforeSend: function (xhr) {
            xhr.setRequestHeader("Authorization", api_key);
        },
		success: function(data, textStatus, jqXHR){
			//alert('Category deleted successfully');
		},
		error: function(jqXHR, textStatus, errorThrown){
			alert('deleteCategory error: ' + errorThrown);
		    var responseText = jQuery.parseJSON(jqXHR.responseText);
		    console.log(responseText);
		    alert(JSON.stringify(responseText));
		}
	});
}

function renderList(data) {
	console.log(data);
	
    //alert(data.categories[1].name);

	var list = data == null ? [] : (data.categories instanceof Array ? data.categories : [data.categories]);
	

	//alert(list);
	$('#categoryList li').remove();

	$.each(list, function(index, category) {
          //console.log( index + ": " + category.id );
          //console.log( index + ": " + category.name);
        $('#categoryList').append('<li class="list-group-item"><a href="#"data-identity="'+ category.id + '" >'+ category.name+'</a></li>');

	});
	
}

function renderDetails(category) {
	$('#categoryId').val(category.id);
	$('#categoryName').val(category.name);
}


function formToJSON() {
	return JSON.stringify({
		"id": $('#categoryId').val(), 
		"name": $('#categoryName').val()
		});
}

function formValues() {
    return {
            id: $('#categoryId').val(),
            category: $('#categoryName').val()
        };
}


