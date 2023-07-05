<?php
session_start();
if ($_SESSION["s_user"] === null && $_SESSION["s_user_id"] === null)
    header("Location: /mino/login.php");
else
    header("Location: /mino/notes.php");
require_once 'mino/db_helper.php';
$connector = new DbHelper();
$connector->createDatabase();
?>