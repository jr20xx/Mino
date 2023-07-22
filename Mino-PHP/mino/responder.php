<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    require_once 'db_helper.php';
    $helper = DbHelper::getInstance();

    $action = (isset($_POST['action'])) ? $_POST['action'] : '';
    $username = (isset($_POST['username'])) ? $_POST['username'] : '';
    $password = hash("sha512", (isset($_POST['password'])) ? $_POST['password'] : '');
    $user_id = $_SESSION["s_user_id"];

    $title = (isset($_POST['title'])) ? $_POST['title'] : '';
    $body = (isset($_POST['body'])) ? $_POST['body'] : '';
    $note_id = (isset($_POST['note_id'])) ? $_POST['note_id'] : '';

    header('Content-Type: application/json');
    switch ($action) {
        case 'authenticate':
            echo $helper->checkCredentials($username, $password);
            break;
        case 'deauthenticate':
            echo $helper->deauthUser();
            exit();
        case 'add_user':
            echo $helper->registerUser($username, $password);
            break;
        case 'update_password':
            echo $helper->changePassword($user_id, $password, hash("sha512", (isset($_POST['newPassword'])) ? $_POST['newPassword'] : ''));
            break;
        case 'remove_account':
            echo $helper->removeAccount($user_id);
            break;
        case 'create_note':
            echo $helper->createNote($title, $body, $user_id);
            break;
        case 'get_notes':
            echo $helper->getNotes($user_id);
            break;
        case 'update_note':
            echo $helper->updateNote($note_id, $title, $body, $user_id);
            break;
        case 'remove_note':
            echo $helper->removeNote($note_id, $user_id);
            break;
        default:
            echo "Action not implemented on this server";
    }
} else {
    echo "Request type not allowed in this server";
}
?>