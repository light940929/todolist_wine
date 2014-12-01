<?php

class DbHandler {

    private $conn;

    function __construct() {
        require_once dirname(__FILE__) . '/DbConnect.php';
        $db = new DbConnect();
        $this->conn = $db->connect();
    }

    public function createUser($name, $email, $password) {
        require_once 'PassHash.php';
        $response = array();

        if (!$this->isUserExists($email)) {
            $password_hash = PassHash::hash($password);
            $api_key = $this->generateApiKey();
            $stmt = $this->conn->prepare("INSERT INTO user(name, email, password_hash, api_key, status) values(?, ?, ?, ?, 1)");
            $stmt->bind_param("ssss", $name, $email, $password_hash, $api_key);

            $result = $stmt->execute();

            $stmt->close();

            if ($result) {
                return USER_CREATED_SUCCESSFULLY;
            } else {
                return USER_CREATE_FAILED;
            }
        } else {
            return USER_ALREADY_EXISTED;
        }

        return $response;
    }

    public function checkLogin($email, $password) {
        $stmt = $this->conn->prepare("SELECT password_hash FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($password_hash);
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->fetch();
            $stmt->close();
            if (PassHash::check_password($password_hash, $password)) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            $stmt->close();
            return FALSE;
        }
    }

    private function isUserExists($email) {
        $stmt = $this->conn->prepare("SELECT id from user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    
    public function refreshUserAPIkey($email) {
    	$api_key = $this->generateApiKey();
    	$stmt = $this->conn->prepare("UPDATE user u SET u.api_key = ? WHERE u.email = ?");
    	$stmt->bind_param("ss", $api_key, $email);
    	$stmt->execute();
    	$num_affected_rows = $stmt->affected_rows;
    	$stmt->close();
    	//return $num_affected_rows > 0;
    }
    
   
    
    public function getUserByEmail($email) {
    	
       $stmt = $this->conn->prepare("SELECT id, name, email, api_key, status, created_at FROM user WHERE email = ?");
       $stmt->bind_param("s", $email);
        
        if ($stmt->execute()) {
            $stmt->bind_result($id, $name, $email, $api_key, $status, $created_at);
            $stmt->fetch();
            $user = array();
            $user["id"] = $id;
            $user["name"] = $name;
            $user["email"] = $email;
            $user["api_key"] = $api_key;
            $user["status"] = $status;
            $user["created_at"] = $created_at;
            $stmt->close();
            return $user;
        } else {
            return NULL;
        }
    }

    public function getApiKeyById($user_id) {
        $stmt = $this->conn->prepare("SELECT api_key FROM user WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            // $api_key = $stmt->get_result()->fetch_assoc();
            // TODO
            $stmt->bind_result($api_key);
            $stmt->fetch();
            $stmt->close();
            return $api_key;
        } else {
            return NULL;
        }
    }
    
    public function getApiKeyByEmail($email) {
    	$stmt = $this->conn->prepare("SELECT api_key FROM user WHERE email = ?");
    	$stmt->bind_param("s", $email);
    	if ($stmt->execute()) {
    		//$api_key = $stmt->get_result()->fetch_assoc();
    		// TODO
    		$stmt->bind_result($api_key);
    		$stmt->fetch();
    		$stmt->close();
    		return $api_key;
    	} else {
    		return NULL;
    	}
    }
    
    public function deleteApiKey($user_id, $api_key) {
    	$stmt = $this->conn->prepare("UPDATE user u SET u.api_key = ? WHERE u.id = ?");
    	$stmt->bind_param("si", $api_key, $user_id);
    	$stmt->execute();
    	$num_affected_rows = $stmt->affected_rows;
    	$stmt->close();
    	return $num_affected_rows > 0;
    }

    public function getUserId($api_key) {
        $stmt = $this->conn->prepare("SELECT id FROM user WHERE api_key = ?");
        $stmt->bind_param("s", $api_key);
        if ($stmt->execute()) {
            $stmt->bind_result($user_id);
            $stmt->fetch();
            $stmt->close();
            return $user_id;
        } else {
            return NULL;
        }
    }

    public function isValidApiKey($api_key) {
        $stmt = $this->conn->prepare("SELECT id from user WHERE api_key = ?");
        $stmt->bind_param("s", $api_key);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    private function generateApiKey() {
        return md5(uniqid(rand(), true));
    }
    
    public function getAllUserToDo($user_id) {
    	$stmt = $this->conn->prepare("SELECT ut.* FROM category ut WHERE ut.user_id = ?");
    	$stmt->bind_param("i", $user_id);
    	$stmt->execute();
    	$categories = $stmt->get_result();
    	$stmt->close();
    	return $categories;
    }
    
    public function createUserToDo($user_id, $new_category_id) {
    	$stmt = $this->conn->prepare("UPDATE category SET user_id = ? WHERE id = ?");
    	$stmt->bind_param("ii", $user_id, $new_category_id);
    	$result = $stmt->execute();
    
    	if (false === $result) {
    		die('execute() failed: ' . htmlspecialchars($stmt->error));
    	}
    	$stmt->close();
    	return $result;
    }
    
    public function createUserToDoList($category_id, $new_list_id) {
    	$stmt = $this->conn->prepare("UPDATE list SET category_id = ? WHERE id = ?");
    	$stmt->bind_param("ii", $category_id, $new_list_id);
    	$result = $stmt->execute();
    
    	if (false === $result) {
    		die('execute() failed: ' . htmlspecialchars($stmt->error));
    	}
    	$stmt->close();
    	return $result;
    }
    
    public function getToDo($category_id, $user_id) {
    	$stmt = $this->conn->prepare("SELECT ut.id, ut.name from category ut WHERE ut.id = ? AND ut.user_id = ?");
    	$stmt->bind_param("ii", $category_id, $user_id);
    	if ($stmt->execute()) {
    		$res = array();
    		$stmt->bind_result($id, $name);
  
    		$stmt->fetch();
    		$res["id"] = $id;
    		$res["name"] = $name;
  
    		$stmt->close();
    		return $res;
    	} else {
    		return NULL;
    	}
    }
    
    public function createToDo($category, $user_id) {
    	$stmt = $this->conn->prepare("INSERT INTO category(name) VALUES(?)");
    	$stmt->bind_param("s", $category);
    	$result = $stmt->execute();
    	$stmt->close();
    
    	if ($result) {
    		$new_category_id = $this->conn->insert_id;
    	    $this->createUserToDo($user_id, $new_category_id);
            return $new_category_id;
       	
    	} else {
    		return NULL;
    	}
    }
    
   public function updateToDo($category_id, $category) {
    	$stmt = $this->conn->prepare("UPDATE category ut SET ut.name = ? WHERE ut.id = ?");
    	$stmt->bind_param("si", $category, $category_id);
    	$stmt->execute();
    	$num_affected_rows = $stmt->affected_rows;
    	$stmt->close();
    	//return $category_id;
    	return $num_affected_rows > 0;
    }
    
   public function deleteToDo($category_id,$user_id) {
    	$stmt = $this->conn->prepare("DELETE ut.* FROM category ut WHERE ut.id = ? AND ut.user_id = ?");
    	$stmt->bind_param("ii", $category_id, $user_id);
    	$stmt->execute();
    	$num_affected_rows = $stmt->affected_rows;
    	$stmt->close();
    	return $num_affected_rows > 0;
    }
    
    public function getAllUserToDoList($user_id, $category_id) {
    	$stmt = $this->conn->prepare("SELECT DISTINCT t.* FROM list t, category ut WHERE ut.user_id = ? AND t.category_id = ?");
    	$stmt->bind_param("ii", $user_id, $category_id);
    	$stmt->execute();
    	$lists = $stmt->get_result();
    	$stmt->close();
    	return $lists;
    }
    
   public function getToDoList($list_id, $category_id, $user_id) {
    	$stmt = $this->conn->prepare("SELECT DISTINCT t.id, t.name from list t, category ut WHERE t.id = ? AND t.category_id = ? AND ut.user_id = ?");
    	$stmt->bind_param("iii", $list_id, $category_id, $user_id);
    	if ($stmt->execute()) {
    		$res = array();
    		$stmt->bind_result($id, $name); //, $status, $created_date
    		$stmt->fetch();
    		$res["id"] = $id;
    		$res["name"] = $name;
    		//$res["status"] = $status;
    		//$res["created_date"] = $created_date;

    		$stmt->close();
    		return $res;
    	} else {
    		return NULL;
    	}
    }
    
    
    public function createToDoList($list, $category_id) {
    	$stmt = $this->conn->prepare("INSERT INTO list(name) VALUES(?)");
    	$stmt->bind_param("s", $list);
    	$result = $stmt->execute();
    	$stmt->close();
    
    	if ($result) {
    		$new_list_id = $this->conn->insert_id;
    		$this->createUserToDoList($category_id, $new_list_id);
    		return $new_list_id;
    
    	} else {
    		return NULL;
    	}
    }
    
    public function updateToDoList($list_id, $list, $status) {
    	$stmt = $this->conn->prepare("UPDATE list t SET t.name = ?, t.status = ? WHERE t.id = ?");
    	$stmt->bind_param("sii", $list, $status, $list_id);
    	$stmt->execute();
    	$num_affected_rows = $stmt->affected_rows;
    	$stmt->close();
    	//return $list_id;
    	return $num_affected_rows > 0;
    }
    
    
    public function deleteToDoList($list_id, $category_id) {
    	$stmt = $this->conn->prepare("DELETE  t.* FROM list t WHERE t.id = ? AND t.category_id = ?");
    	$stmt->bind_param("ii", $list_id, $category_id);
    	$stmt->execute();
    	$num_affected_rows = $stmt->affected_rows;
    	$stmt->close();
    	return $num_affected_rows > 0;
    }
   

  
}

?>
