<?php
session_start();
require_once '../mino/db_helper.php';
$helper = new DbHelper();
$connection = $helper->connect();

$username = (isset($_POST['username'])) ? $_POST['username'] : '';
$password = hash("sha512", (isset($_POST['password'])) ? $_POST['password'] : '');
$operation_type = (isset($_POST['operation_type'])) ? $_POST['operation_type'] : '';
$user_id = $_SESSION["s_user_id"];
$resultCode = null;

switch ($operation_type) {
    case 0: //CREATE
        if (!$helper->isUsernameRegistered($username)) {
            $query = "INSERT INTO USERS(USERNAME, PASSWORD) VALUES(:username, :password)";
            $result = $connection->prepare($query);
            $result->bindParam(':username', $username);
            $result->bindParam(':password', $password);
            $result->execute();
            $resultCode = $result->rowCount() >= 1 ? 201 : null;
        } else
            $resultCode = 400;
        break;
    case 1: //READ
        break;
    case 2: //UPDATE
        $newPassword = hash("sha512", (isset($_POST['newPassword'])) ? $_POST['newPassword'] : '');
        $query = "UPDATE USERS SET PASSWORD=:newPassword WHERE ID=:user_id AND PASSWORD=:password;";
        $result = $connection->prepare($query);
        $result->bindParam(':newPassword', $newPassword);
        $result->bindParam(':user_id', $user_id);
        $result->bindParam(':password', $password);
        $result->execute();
        $resultCode = $result->rowCount() >= 1 ? 200 : null;
        break;
    case 3: //DELETE
        $query = "DELETE FROM USERS WHERE ID=" . $user_id . ";";
        $result = $connection->prepare($query);
        $result->execute();
        if ($result->rowCount() >= 1) {
            unset($_SESSION["s_user"]);
            unset($_SESSION["s_user_id"]);
            session_destroy();
            echo 200;
            exit();
        }
        break;
}

echo $resultCode;
$connection = null;
?>