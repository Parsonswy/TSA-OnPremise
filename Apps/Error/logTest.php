<?php
$error = new Exception("My test exception", 34);
require("./Logger.php");

$logger = new Logger();
$logger->draftLog($error);
echo "done";
?>