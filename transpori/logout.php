<?php
session_start();
session_destroy();
header('Location: http://localhost/PFA/transpori/home/transpori.php');
exit();
?>