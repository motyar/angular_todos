<?php

require 'Slim/Slim.php';

$app = new Slim();

$app->get('/todos', 'getTodos');
$app->post('/todos', 'addTodo');
$app->delete('/todos/:id',	'deleteTodo');

$app->run();

function getTodos() {
	$sql = "select * FROM todos ORDER BY todo";
	try {
		$db = getConnection();
		$stmt = $db->query($sql);  
		$wines = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo json_encode($wines);
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}


function addTodo() {
	$request = Slim::getInstance()->request();
	$todo = json_decode($request->getBody());
	$sql = "INSERT INTO todos (todo) VALUES (:todo)";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("todo", $todo->todo);
		$stmt->execute();
		$todo->id = $db->lastInsertId();
		$db = null;
		echo json_encode($todo); 
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}


function deleteTodo($id) {
	$sql = "DELETE FROM todos WHERE id=:id";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("id", $id);
		$stmt->execute();
		$db = null;
        echo json_encode(array('done'=>'yes'));
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}


function getConnection() {
	$dbhost="127.0.0.1";
	$dbuser="root";
	$dbpass="dempass";
	$dbname="todos";
	$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);	
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $dbh;
}

?>
