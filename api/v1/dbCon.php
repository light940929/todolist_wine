<?php
	
	function getConnection() {
		$dbh = new PDO("mysql:host=". DB_HOST. ";dbname=". DB_NAME, DB_USERNAME , DB_PASSWORD);
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return $dbh;
	}

?>