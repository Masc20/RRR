<?php  

session_start();

unset($_SESSION['userType']);
unset($_SESSION['userId']);

header("location: ../");

?>