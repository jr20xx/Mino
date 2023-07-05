<?php
session_start();
unset($_SESSION["s_user"]);
unset($_SESSION["s_user_id"]);
session_destroy();
header("Location: ../index.php");
exit();
?>