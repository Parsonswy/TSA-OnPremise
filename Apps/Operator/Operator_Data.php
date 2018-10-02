<?php
  class Operator{
    private $_op_id;//database id
      public function getOpID(){return $this->_op_id;}

    private $_op_friendly_name;//display name
      public function getFriendlyName(){return $this->_op_friendly_name;}

    private $_op_instance_id;//php_ssid
      public function getOpInstanceId(){return $this->_op_instance_id;}

    private $_op_permission_level;//permission level
      public function getOpPermissionLevel(){return $this->_op_permission_level;}

    private $_ip_addr;
      public function getOpIpAddr(){return $this->_ip_addr;}

    private $_mysqli;

    public function __construct($op_id){
      $this->_mysqli = new TSADB();
      $this->_mysqli = $this->_mysqli->gsql();
      $this->_op_id = $op_id;
      $this->_op_instance_id = session_id();
      $this->_loadOpData();
    }

    private function _loadOpData(){
      $query = $this->_mysqli->prepare("SELECT `permission_level`,`display_name` FROM `op_credentials` WHERE `id`=? LIMIT 1");
      $query->bind_param("i", $this->_op_id);
      $query->execute();
      $query->store_result();
      if($query->num_rows !== 1){
        dieOfError(102, "[OP_102]Error accessing data from server.");
        //kills request
      }

      $query->bind_result($this->_op_permission_level, $this->_op_friendly_name);
      $query->fetch();
      $query->close();
    }
  }
?>
