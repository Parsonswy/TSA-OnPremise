<?php
require("./Apps/User/User.php");
$User = new User(273549);
echo "<pre>";
$foo = $User->getAccountProperty(array("name","balance","active"));
var_dump($foo);
echo "</pre>"
?>