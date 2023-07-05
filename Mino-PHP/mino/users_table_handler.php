<?php
require_once '../mino/db_helper.php';
$helper = new DbHelper();
$connection = $helper->connect();

$username = (isset($_POST['username'])) ? $_POST['username'] : '';
$password = hash("sha512", (isset($_POST['password'])) ? $_POST['password'] : '');
$operation_type = (isset($_POST['operation_type'])) ? $_POST['operation_type'] : '';

switch ($operation_type) {
    case 0: //CREATE
        if (!$helper->isUsernameRegistered($username)) {
            $query = "INSERT INTO USERS(USERNAME, PASSWORD) VALUES(:username, :password)";
            $result = $connection->prepare($query);
            $result->bindParam(':username', $username);
            $result->bindParam(':password', $password);
            $result->execute();
            echo $result->rowCount() >= 1 ? 201 : 400;
        } else
            echo 400;
        break;
    case 1: //READ
        break;
    case 2: //UPDATE
        break;
    case 3: //DELETE
        break;
}
?>