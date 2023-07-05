<?php
session_start();

require_once '../mino/db_helper.php';
$helper = new DbHelper();
$connection = $helper->connect();

$title = (isset($_POST['title'])) ? $connection->quote($_POST['title']) : '';
$body = (isset($_POST['body'])) ? $connection->quote($_POST['body']) : '';
$operation_type = (isset($_POST['operation_type'])) ? $_POST['operation_type'] : '';
$note_id = (isset($_POST['note_id'])) ? $_POST['note_id'] : '';
$user_id = $_SESSION["s_user_id"];

switch ($operation_type) {
    case 0: //Create
        $current_time = time();
        $query = "INSERT INTO NOTES(TITLE, BODY, TIME_STAMP, USER_ID) VALUES(" . $title . ", " . $body . ", " . $current_time . ", " . $user_id . ");";
        $result = $connection->prepare($query);
        $result->execute();
        $query = "SELECT * FROM NOTES ORDER BY ID DESC LIMIT 1";
        $result = $connection->prepare($query);
        $result->execute();
        $data = $result->fetchAll(PDO::FETCH_ASSOC);
        break;
    case 1: //Read
        $query = "SELECT * FROM NOTES WHERE USER_ID='$user_id' ORDER BY TIME_STAMP;";
        $result = $connection->prepare($query);
        $result->execute();
        $data = $result->fetchAll(PDO::FETCH_ASSOC);
        break;
    case 2: //Update
        $current_time = time();
        $query = "UPDATE NOTES SET TITLE=" . $title . ", BODY=" . $body . ", TIME_STAMP=" . $current_time . " WHERE ID=" . $note_id . " AND USER_ID=" . $user_id . ";";
        $result = $connection->prepare($query);
        $result->execute();
        $query = "SELECT * FROM NOTES WHERE ID='$note_id' AND USER_ID='$user_id'";
        $result = $connection->prepare($query);
        $result->execute();
        $data = $result->fetchAll(PDO::FETCH_ASSOC);
        break;
    case 3: //Delete
        $query = "DELETE FROM NOTES WHERE ID='$note_id' AND USER_ID ='$user_id'";
        $result = $connection->prepare($query);
        $data = $result->execute();
        break;
}

header('Content-Type: application/json');
echo json_encode($data, JSON_UNESCAPED_UNICODE);
$connection = NULL;
?>