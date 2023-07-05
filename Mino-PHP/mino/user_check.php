<?php
session_start();

require_once 'db_helper.php';
$connector = new DbHelper();
$connection = $connector->connect();
$user = (isset($_POST['user'])) ? $_POST['user'] : '';
$password = hash("sha512", (isset($_POST['password'])) ? $_POST['password'] : '');

$query = "SELECT * FROM USERS WHERE USERNAME=:user AND PASSWORD=:password;";
$result = $connection->prepare($query);
$result->bindParam(':user', $user);
$result->bindParam(':password', $password);
$result->execute();
$rows = $result->fetchAll(PDO::FETCH_ASSOC);

if (count($rows) >= 1) {
    $_SESSION["s_user"] = $user;
    $_SESSION["s_user_id"] = $rows[0]['ID'];
} else {
    $_SESSION["s_user"] = null;
    $_SESSION["s_user_id"] = null;
    $rows = null;
}

header('Content-Type: application/json');
echo json_encode($rows, JSON_UNESCAPED_UNICODE);
$connection = NULL;
?>