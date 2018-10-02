<?php
Class User{
  private $_mysqli;

  private $uuid;
    public function getUUID(){return $this->uuid;}

  private $name;
    public function getName(){return $this->name;}

  private $balance;
	public function getBalance(){return $this->balance;}
  private $account_status;
    public function getAccountStatus(){return $this->account_status;}

  private $pin_needed;
    public function isPinNeeded(){return $this->pin_needed;}

  private $photoID_needed;
    public function isPhotoIDNeeded(){return $this->photoID_needed;}

/* Non essential data */
  private $notes;
  public function getNotes(){return $this->notes;}

  public function __construct($data){
    GLOBAL $mysqli;
    $this->_mysqli = $mysqli;
      switch(gettype($data)){
        case "array"://assoc array of fields
          foreach($data as $key=>$value){
            $this->$key = $value;
          }
        break;case "integer"://uuid
            $this->uuid = $data;
            if($this->uuid == -1){
              $this->account_status = 0;
              return true;
            }
        break;case "string":
          $this->uuid = $data;
          if($this->uuid == -1){
            $this->account_status = 0;
            return true;
          }
        break; default:
          var_dump($data);
          header("tsastatus:500");
          exit(json_encode(array("type"=>1, "message"=>"Unexpected Data Input Error.", "time"=>8000)));
        break;
      }

    if(isset($this->uuid))
        $this->populateObject();

    if(!$this->validate()){
      header("tsastatus:500");
      exit(json_encode(array("type"=>1, "message"=>"Unexpected Data Validation Error.", "time"=>8000)));
    }
  }

  private function populateObject(){
    if(!isset($this->uuid)){
      return false;
    }
    $query = $this->_mysqli->prepare("SELECT `balance`,`display_name`,`account_status`,`pin` IS NOT NULL, `picture_id` IS NOT NULL FROM `client_info` WHERE `uuid`=?");
    $query->bind_param("i",$this->uuid);
    $query->execute();
    $query->store_result();

    if($query->num_rows != 1)
      return false;

    $query->bind_result($this->balance,$this->name, $this->account_status, $this->pin_needed, $this->photoID_needed);
    $query->fetch();
    return true;
  }

  private function validate(){
    if(isset($this->uuid)){
      if($this->uuid >= 100000 && $this->uuid <= 999999){
        //is integer within uuid range
      }else
        return false;
    }

    if(isset($this->name)){
      if(gettype($this->name) != "string")
        return false;
    }

    if(isset($this->account_status)){
      if($this->account_status >= -1 && $this->account_status <= 3){
        //is valid account status
      }else
        return false;
    }else
      $this->account_status = -1;//Assumes DNE b/c query should supply this value beforehand

    if(isset($this->notes)){
      if(gettype($this->notes) != "string")
        return false;
    }

    return true;
  }
}
?>
