<?php
require(CONFIG::DOC_ROOT . "/Apps/Commerce/storefront_config.php");
class Store{
  private $_storeConfig;
  public function __construct(){

  }

  public function loadStorefront(){
    $this->_storeConfig = getStorefront();
    Global $Operator;
    $User = $Operator->getAccountsInterface()->getUser();
    $this->_storeConfig["client_uuid"] = ($User->getUUID() != -1? $User->getUUID():"No Client");
    $this->_storeConfig["client_name"] = ($User->getName() !=null? $User->getName():"No Client");
    header("tsastatus:2005");
    exit(json_encode($this->_storeConfig));
  }
}
?>
