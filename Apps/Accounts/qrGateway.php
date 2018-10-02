<?php
//http://172.16.0.2544321`/TSA/qrGateway.php?UUID=(6 digit #)
require("/var/www/html/Development/config.php");
header("Location: " . CONFIG::DOC_ROOT_WEB . "/TSA_Auction.php?for=2002&data=" . $_GET["UUID"]);
exit();
?>
