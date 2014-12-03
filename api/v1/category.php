<?php
function getCategories() {
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
}

function getCategory($id) {
	//echo $id;
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
}


function addCategory() {
	error_log('addCategory\n', 3, '/var/tmp/php.log');
	$request = Slim::getInstance()->request();
	$category = json_decode($request->getBody());

	$sql = "INSERT INTO category (name, user_id) VALUES (:name, :description, :user_id)";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("name", $category->name);
		$stmt->bindParam("user_id", $category->user_id);

		$stmt->execute();
		
		$category->id = $db->lastInsertId();
		$db = null;
		echo json_encode($category);
	} catch(PDOException $e) {
		error_log($e->getMessage(), 3, '/var/tmp/php.log');
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
}

function updateCategory($id) {
	//$request = Slim::getInstance()->request();
	
	$request = \Slim\Slim::getInstance()->request();
	$body = $request->getBody();
	$category = json_decode($body);
	$sql = "UPDATE category SET name=:name WHERE id=:id";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("name", $category->name);
		$stmt->bindParam("id", $id);
		$stmt->execute();
		$db = null;
		echo json_encode($category);
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
}

function deleteCategory($id) {
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
}

function findByName($query) {
	$sql = "SELECT * FROM category WHERE UPPER(name) LIKE :query ORDER BY name";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$query = "%".$query."%";
		$stmt->bindParam("query", $query);
		$stmt->execute();
		$categories = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"category": ' . json_encode($categories) . '}';
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
}


?>