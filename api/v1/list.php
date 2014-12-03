<?php
function getLists($id) {
	//echo $id;
	$sql = "SELECT * FROM list WHERE category_id=".$id ." ORDER BY name";
	
	try {
		$db = getConnection();
		$stmt = $db->query($sql);
		$lists = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"list": ' . json_encode($lists) . '}';
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
}

function getList($id, $list_id) {
	//echo $id;
	//echo $list_id;
	$sql = "SELECT * FROM list WHERE id=".$list_id ." AND category_id=".$id ." ORDER BY name";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("id", $list_id);
		$stmt->execute();
		$list = $stmt->fetchObject();
		$db = null;
		echo json_encode($list);
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
}


function addList() {
	error_log('addList\n', 3, '/var/tmp/php.log');
	$request = Slim::getInstance()->request();
	$list = json_decode($request->getBody());

	$sql = "INSERT INTO list (name, created_date, finished_date, category_id) VALUES (:name, :created_date, :finished_date, :category_id)";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("name", $list->name);
		$stmt->bindParam("created_date", $list->created_date);
		$stmt->bindParam("finished_date", $list->finished_date);
		$stmt->bindParam("category_id", $list->category_id);

		$stmt->execute();
		$list->id = $db->lastInsertId();
		$db = null;
		echo json_encode($list);
	} catch(PDOException $e) {
		error_log($e->getMessage(), 3, '/var/tmp/php.log');
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
}

function updateList($id, $list_id) {
	$request = Slim::getInstance()->request();
	$body = $request->getBody();
	$list = json_decode($body);
	$sql = "UPDATE list SET name=:name, finished_date=:finished_date WHERE id=".$list_id;
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("name", $list->name);
		$stmt->bindParam("finished_date", $list->finished_date);
		$stmt->bindParam("id", $list_id);
		$stmt->execute();
		$db = null;
		echo json_encode($list);
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
}

function deleteList($id, $list_id) {
	$sql = "DELETE FROM list WHERE id=".$list_id;
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("id", $list_id);
		$stmt->execute();
		$db = null;
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
}

function findByNameList($query) {
	$sql = "SELECT * FROM list WHERE UPPER(name) LIKE :query ORDER BY name";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$query = "%".$query."%";
		$stmt->bindParam("query", $query);
		$stmt->execute();
		$categories = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"list": ' . json_encode($categories) . '}';
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
}

?>