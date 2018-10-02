/*
  Main loader on TSA_Manager.php runs on first load to initialize "web app"
    Management actions performed beyond that are handled by Manage.php
*/

//Load Navigation - Will also check for login
netSend(management + "?action=2000", "GET", "","");
