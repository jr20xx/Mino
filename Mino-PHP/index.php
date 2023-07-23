<?php
session_start();
require_once 'mino/db_helper.php';
$helper = DbHelper::getInstance();
$helper->createDatabase();

if (($_SESSION["s_user"] === null && $_SESSION["s_user_id"] === null) || !$helper->isUernameRegistered($_SESSION["s_user"]))
    header("Location: /mino/login.php");
else
    header("Location: /mino/notes.php");
?>