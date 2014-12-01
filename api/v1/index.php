<?php
require_once '../../include/DbHandler.php';
require_once '../../include/PassHash.php';
require '../.././include/config.php';
require '../.././libs/Slim/Slim.php';
include 'dbCon.php';
//include 'category.php';
//include 'list.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

	
$app->post('/register', function() use ($app) {
            verifyRequiredParams(array('name', 'email', 'password'));
            $response = array();
            $name = $app->request->post('name');
            $email = $app->request->post('email');
            $password = $app->request->post('password');
            validateName($name);
            validateEmail($email);
            

            $db = new DbHandler();
            $res = $db->createUser($name, $email, $password);

            if ($res == USER_CREATED_SUCCESSFULLY) {
                $response["error"] = false;
                $response["message"] = "You are successfully registered";
                echoRespnse(201, $response);
            } else if ($res == USER_CREATE_FAILED) {
                $response["error"] = true;
                $response["message"] = "Oops! An error occurred while registereing";
                echoRespnse(400, $response);
            } else if ($res == USER_ALREADY_EXISTED) {
                $response["error"] = true;
                $response["message"] = "Sorry, this email already existed";
                echoRespnse(400, $response);
            }
        });

$app->post('/login', function() use ($app) {
            verifyRequiredParams(array('email', 'password'));

            $email = $app->request()->post('email');
            $password = $app->request()->post('password');
            $response = array();
            $db = new DbHandler();
                        
            if ($db->checkLogin($email, $password)) {
          
            	    $db->refreshUserAPIkey($email);
	                $user = $db->getUserByEmail($email);
	
	                if ($user != NULL) {
	                    $response["error"] = false;
	                    $response['id'] = $user['id'];
	                    $response['name'] = $user['name'];
	                    $response['email'] = $user['email'];
	                    $response['apiKey'] = $user['api_key'];
	                    $response['createdAt'] = $user['created_at'];
	                    
	                    echoRespnse(201, $response);
	                } else {
	                    $response['error'] = true;
	                    $response['message'] = "An error occurred. Please try again";
	                    echoRespnse(400, $response);
	                } 
        
            } else {
                $response['error'] = true;
                $response['message'] = 'Login failed. Incorrect credentials';
                echoRespnse(400, $response);
            }

            
        });


$app->delete('/login/:user_id', 'authenticate', function($user_id) use ($app) {

	$db = new DbHandler();
	$response = array();
	$api_key=Null;
	$result = $db->deleteApiKey($user_id, $api_key);

	if ($result) {
		$response["error"] = false;
		$response["message"] = "Logout successfully";
		echoRespnse(200, $response);
	} else {
		$response["error"] = true;
		$response["message"] = "Logout failed . Please try again!";
		echoRespnse(400, $response);
	}
	});


$app->get('/categories', 'authenticate', function() {
            global $user_id;
            $response = array();
            $db = new DbHandler();
            $result = $db->getAllUserToDo($user_id);
            //$response["error"] = false;
            $response["categories"] = array();
            while ($category = $result->fetch_assoc()) {
                $tmp = array();
                $tmp["id"] = $category ["id"];
                $tmp["name"] = $category ["name"];
                array_push($response["categories"], $tmp);
            }
            echoRespnse(200, $response);
        });

$app->get('/categories/:id', 'authenticate', function($category_id) {
            global $user_id;
            $response = array();
            $db = new DbHandler();
            $result = $db->getToDo($category_id, $user_id);

            if ($result != NULL) {
                $response["error"] = false;
                $response["id"] = $result["id"];
                $response["name"] = $result["name"];
                echoRespnse(200, $response);
            } else {
                $response["error"] = true;
                $response["message"] = "The requested resource doesn't exists";
                echoRespnse(404, $response);
            }
        });

$app->post('/categories', 'authenticate', function() use ($app) {
            verifyRequiredParams(array('category'));
            $response = array();
            $category = $app->request->post('category');
            global $user_id;
            $db = new DbHandler();
            $category_id = $db->createToDo($category, $user_id);

            if ($category_id != NULL) {
                $response["error"] = false;
                $response["message"] = "Category created successfully";
                $response["category_id"] = $category_id;

                echoRespnse(201, $response);
            } else {
                $response["error"] = true;
                $response["message"] = "Failed to create category. Please try again";
                echoRespnse(400, $response);
            }            
        });

/*$app->put('/categories/:id', 'authenticate', function($category_id) use($app) {
            
	        //echo "category_id:".$category_id;
	        verifyRequiredParams(array('category'));

            $category = $app->request->post('category');
            //echo "category:".$category;
            $db = new DbHandler();
            $response = array();

            $result = $db->updateToDo($category_id, $category);
            if ($result) {
                $response["error"] = false;
                $response["message"] = "Category updated successfully";
            } else {
                $response["error"] = true;
                $response["message"] = "Category failed to update. Please try again!";
            }
            echoRespnse(200, $response);
        });*///   "message": "Required field(s) category is missing or empty"



$app->put('/categories/:id', 'authenticate', function ($category_id) use ($app)  {
		try {
			$request = $app->request();
			$input = json_decode($request->getBody());
			$sql = "UPDATE category ut SET ut.name = :name WHERE ut.id =".$category_id.";";
	
			$db = getConnection();
			$stmt = $db->prepare($sql);
			$stmt->bindParam("name", $input->name);	
			$stmt->execute();
			$db = null;

			echo json_encode($input);
			$app->response()->status(200);
	
		} catch (Exception $e) {
			$app->response()->status(400);
			$app->response()->header('X-Status-Reason', $e->getMessage());
			$app->$response["error"] = true;
			$app->$response["message"] = "The requested resource doesn't exists";
			echoRespnse(404, $response);
		}
	});

$app->delete('/categories/:id', 'authenticate', function($category_id) use($app) {
            global $user_id;

            $db = new DbHandler();
            $response = array();
            $result = $db->deleteToDo($category_id, $user_id);
            if ($result) {
                $response["error"] = false;
                $response["message"] = "Category deleted successfully";
                echoRespnse(200, $response);
            } else {
                $response["error"] = true;
                $response["message"] = "Category failed to delete. Please try again!";
                echoRespnse(400, $response);
            }
           
        });

$app->get('/categories/:id/lists', 'authenticate', function($category_id) {
		global $user_id;
		$response = array();
		$db = new DbHandler();
		$result = $db->getAllUserToDoList($user_id, $category_id);
		$response["error"] = false;
		$response["lists"] = array();
		while ($list = $result->fetch_assoc()) {
			$tmp = array();
			$tmp["id"] = $list ["id"];
			$tmp["name"] = $list ["name"];
			$tmp["status"] = $list ["status"];
			$tmp["created_date"] = $list["created_date"];
			array_push($response["lists"], $tmp);
		}
	
		echoRespnse(200, $response);
	});

$app->get('/categories/:id/lists/:list_id', 'authenticate', function($category_id, $list_id) {
		global $user_id;
		$response = array();
		$db = new DbHandler();
		$result = $db->getToDoList($list_id, $category_id, $user_id);
	
		if ($result != NULL) {
			$response["error"] = false;
			$response["id"] = $result["id"];
			$response["name"] = $result["name"];
			//$response["status"] = $result["status"];
			//$response["created_date"] = $result["created_date"];

			echoRespnse(200, $response);
		} else {
			$response["error"] = true;
			$response["message"] = "The requested resource doesn't exists";
			echoRespnse(404, $response);
		}
	});

$app->post('/categories/:id/lists', 'authenticate', function($category_id) use ($app) {
		verifyRequiredParams(array('list'));
		$response = array();
		$list = $app->request->post('list');
		$db = new DbHandler();
		$list_id = $db->createToDoList($list, $category_id);

		if ($list_id != NULL) {
			$response["error"] = false;
			$response["message"] = "List created successfully";
			$response["list_id"] = $list_id;
	
			echoRespnse(201, $response);
		} else {
			$response["error"] = true;
			$response["message"] = "Failed to create list. Please try again";
			echoRespnse(400, $response);
		}
	});

/*$app->put('/categories/:id/lists/:list_id', 'authenticate', function($category_id, $list_id) use($app) {
		
	    //echo "list_id:".$list_id;
	    verifyRequiredParams(array('list', 'status'));
	
		$list = $app->request->put('list');
		$status = $app->request->put('status');
		//echo "list:".$list;
		$db = new DbHandler();
		$response = array();
	
		$result = $db->updateToDoList($list_id, $list, $status);
		if ($result) {
			$response["error"] = false;
			$response["message"] = "List updated successfully";
		} else {
			$response["error"] = true;
			$response["message"] = "List failed to update. Please try again!";
		}
		echoRespnse(200, $response);
	});*/
	
$app->put('/categories/:id/lists/:list_id', 'authenticate', function ($category_id, $list_id) use ($app)  {
		try {
			$request = $app->request();
			$input = json_decode($request->getBody());
			
			$sql = "UPDATE list t SET t.name = :name, t.status = :status WHERE t.id = ".$list_id.";";
	
			$db = getConnection();
			$stmt = $db->prepare($sql);
			$stmt->bindParam("name", $input->name);
			$stmt->bindParam("status", $input->status);
			$stmt->execute();
			$db = null;
	
			echo json_encode($input);
			$app->response()->status(200);
	
		} catch (Exception $e) {
			$app->response()->status(400);
			$app->response()->header('X-Status-Reason', $e->getMessage());
			$app->$response["error"] = true;
			$app->$response["message"] = "The requested resource doesn't exists";
			echoRespnse(404, $response);
		}
	});



$app->delete('/categories/:id/lists/:list_id', 'authenticate', function($category_id, $list_id) use($app) {
		$db = new DbHandler();
		$response = array();
		$result = $db->deleteToDoList($list_id, $category_id);
		if ($result) {
			$response["error"] = false;
			$response["message"] = "List deleted successfully";
			echoRespnse(200, $response);
		} else {
			$response["error"] = true;
			$response["message"] = "List failed to delete. Please try again!";
			echoRespnse(400, $response);
		}
		
	});


	function verifyRequiredParams($required_fields) {
	    $error = false;
	    $error_fields = "";
	    $request_params = array();
	    $request_params = $_REQUEST;
	    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
	        $app = \Slim\Slim::getInstance();
	        parse_str($app->request()->getBody(), $request_params);	        
	       //echo $app->request()->getBody();
	       //echo $request_params[0];
	        
	    }
	    
	    foreach ($required_fields as $field) {
	        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
	            $error = true;
	            $error_fields .= $field . ', ';
	        }
	    }
	 
	    if ($error) {
	        $response = array();
	        $app = \Slim\Slim::getInstance();
	        $response["error"] = true;
	        $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
	        echoRespnse(400, $response);
	        $app->stop();
	    }
	}

	function validateEmail($email) {
	    $app = \Slim\Slim::getInstance();
	    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
	        $response["error"] = true;
	        $response["message"] = 'Email address is not valid';
	        echoRespnse(400, $response);
	        $app->stop();
	    }
	}
	
	function validateName($name) {
		$app = \Slim\Slim::getInstance();		
		if(!preg_match("/^[a-zA-Z'-]+$/",$name)) {
			$response["error"] = true;
			$response["message"] = 'Name is not valid';
			echoRespnse(400, $response);
			$app->stop();
		}
	
	}

	function echoRespnse($status_code, $response) {
	    $app = \Slim\Slim::getInstance();
	    $app->status($status_code);
	    $app->contentType('application/json');
	
	    echo json_encode($response);
	}

	function authenticate(\Slim\Route $route) {
	
		$headers = apache_request_headers();
		$response = array();
		$app = \Slim\Slim::getInstance();
	
		if (isset($headers['Authorization'])) {
			$db = new DbHandler();
			$api_key = $headers['Authorization'];
			if (!$db->isValidApiKey($api_key)) {
				$response["error"] = true;
				$response["message"] = "Access Denied. Invalid Api key";
				echoRespnse(401, $response);
				$app->stop();
			} else {
				global $user_id;
				$user_id = $db->getUserId($api_key);
			}
		} else {
			$response["error"] = true;
			$response["message"] = "Api key is misssing";
			echoRespnse(400, $response);
			$app->stop();
		}
	}	
	
	
$app->run();

?>