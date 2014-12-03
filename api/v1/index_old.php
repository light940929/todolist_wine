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
            validateEmail($email);

            $db = new DbHandler();
            $res = $db->createUser($name, $email, $password);

            if ($res == USER_CREATED_SUCCESSFULLY) {
                $response["error"] = false;
                $response["message"] = "You are successfully registered";
            } else if ($res == USER_CREATE_FAILED) {
                $response["error"] = true;
                $response["message"] = "Oops! An error occurred while registereing";
            } else if ($res == USER_ALREADY_EXISTED) {
                $response["error"] = true;
                $response["message"] = "Sorry, this email already existed";
            }
            echoRespnse(201, $response);
        });

$app->post('/login', function() use ($app) {
            verifyRequiredParams(array('email', 'password'));

            $email = $app->request()->post('email');
            $password = $app->request()->post('password');
            $response = array();

            $db = new DbHandler();
            if ($db->checkLogin($email, $password)) {
                $user = $db->getUserByEmail($email);

                if ($user != NULL) {
                    $response["error"] = false;
                    $response['name'] = $user['name'];
                    $response['email'] = $user['email'];
                    $response['apiKey'] = $user['api_key'];
                    $response['createdAt'] = $user['created_at'];
                } else {
                    $response['error'] = true;
                    $response['message'] = "An error occurred. Please try again";
                }
            } else {
                $response['error'] = true;
                $response['message'] = 'Login failed. Incorrect credentials';
            }

            echoRespnse(200, $response);
        });
        

$app->get('/categories', 'authenticate', function() {
	$sql = "SELECT * FROM category ORDER BY name";
	try {
		$db = getConnection();
		$stmt = $db->query($sql);
		$categories = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"category": ' . json_encode($categories) . '}';
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
});

$app->get('/categories/:id', 'authenticate', function($id) {
		global $user_id;
	    $sql = "SELECT * FROM category WHERE id=:id";
		try {
			$db = getConnection();
			$stmt = $db->prepare($sql);
			$stmt->bindParam("id", $id);
			$stmt->execute();
			$category = $stmt->fetchObject();
			$db = null;
			echo json_encode($category);
		} catch(PDOException $e) {
			echo '{"error":{"text":'. $e->getMessage() .'}}';
		}
		});
	
$app->post('/categories', 'authenticate', function() use ($app) {
	    error_log('addCategory\n', 3, '/var/tmp/php.log');
		//$request = Slim::getInstance()->request();
		
		$request = $app->request();
		$category = json_decode($request->getBody());
	
		$sql = "INSERT INTO category (name) VALUES (:name)";
		try {
			$db = getConnection();
			$stmt = $db->prepare($sql);
			$stmt->bindParam("name", $category->name);
			$stmt->execute();
			$category->id = $db->lastInsertId();
			$db = null;
			echo json_encode($category);
		} catch(PDOException $e) {
			error_log($e->getMessage(), 3, '/var/tmp/php.log');
			echo '{"error":{"text":'. $e->getMessage() .'}}';
		}
		});

$app->put('/categories/:id', 'authenticate', function ($id) use ($app)  {
		try {
			$request = $app->request();
			$input = json_decode($request->getBody());
			$sql = "UPDATE category ut SET ut.name = :name WHERE ut.id =".$id.";";
	
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
		}
	});

$app->delete('/categories/:id', 'authenticate', function($id) use($app) {
	$sql = "DELETE FROM category WHERE id=:id";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("id", $id);
		$stmt->execute();
		$db = null;
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}';
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