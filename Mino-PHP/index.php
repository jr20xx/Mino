<?php
session_start();
require_once 'mino/db_helper.php';
$helper = DbHelper::getInstance();
$helper->createDatabase();

if ((empty($_SESSION["s_user"]) && empty($_SESSION["s_user_id"])) || !$helper->isUernameRegistered($_SESSION["s_user"]))
    header("Location: /mino/login.php");
else
    header("Location: /mino/notes.php");
?>