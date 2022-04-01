<?php
session_start();
setcookie("id_uporabnika", $_SESSION["id_uporabnika"], time() - 3600);
session_destroy();
header("location: ../");
exit();
?>