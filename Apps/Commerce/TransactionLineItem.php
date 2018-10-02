<?php
class LineItem{
  private $_mysqli;

  private $_id;
    public function getLineItemId(){return $this->_id;}
  private $_sku;
    public function getSKU(){return $this->_sku;}
  private $_item_id;
  private $_desc;
    public function getDesc(){return $this->_desc;}
  private $_value;
  private $_price;
    public function getPrice(){return $this->_price;}
  private $_unitPrice;
  private $_chargeType;
    public function getChargeType(){return $this->_chargeType;}
  private $_parent;//id

  public function __construct($id = null, $parent, $item_id = null, $_value = null){
    GLOBAL $mysqli;
    $this->_mysqli = $mysqli;

    $this->_id = $id;
    $this->_item_id = $item_id;
    $this->_value = $_value;
    $this->_parent = $parent;

    if(isset($this->_id)){//load existing line item
      if(!$this->populate_fields()){
        header("tsastatus: 500");
        exit(json_encode(array("type"=>1, "message"=>"[LIPF]Database Error!", "time"=>8000)));
      }
    }else{//create new line item from item_id
      if(!$this->generate_fields()){
        header("tsastatus: 500");
        exit(json_encode(array("type"=>1, "message"=>"[LIGF]Database Error!", "time"=>8000)));
      }
    }
  }

  //
  //Get data from pre-existing line item
  //
  private function populate_fields(){
    $query = $this->_mysqli->prepare("SELECT `item_id`,`quantity`,`price` FROM `trans_line_items` WHERE `id`=?");
    $query->bind_param("i",$this->_id);
    $query->execute();
    $query->store_result();
    $query->bind_result($this->_item_id, $this->_value, $this->_price);
    $query->fetch();
    if($query->num_rows != 1)
      return false;

    $query = $this->_mysqli->prepare("SELECT `SKU`,`chargeType`,`price` FROM `trans_purchasable` WHERE `id`=?");
    $query->bind_param("i",$this->_item_id);
    $query->execute();
    $query->store_result();
    $query->bind_result($this->_sku, $this->_chargeType, $this->_unitPrice);
    $query->fetch();

    if($query->num_rows != 1)
      return false;

    return true;
  }

  //
  //Compile data for new line item
  //
  private function generate_fields(){
    $query = $this->_mysqli->prepare("SELECT `price`,`SKU`,`chargeType` FROM `trans_purchasable` WHERE `id`=?");
    $query->bind_param("i",$this->_item_id);
    $query->execute();
    $query->bind_result($this->_unitPrice, $this->_sku, $this->_chargeType);
    $query->store_result();
    $query->fetch();
    if($query->num_rows != 1)
      return false;

    $this->calculateItemPrice();

    // if($this->_value > $stock){
    //   header("tsastatus:2100.1");
    //   exit("STOCK!");
    // }

    return true;
  }

  //generate JSON string
  public function genJString(){
    $query = $this->_mysqli->prepare("SELECT `name` FROM `trans_stock` WHERE `SKU`=?");
    $query->bind_param("i",$this->_sku);
    $query->execute();
    $query->store_result();

    if($query->num_rows != 1){
      header("tsastatus: 500");
      exit(json_encode(array("type"=>1, "message"=>"Error Parsing Line Item", "time"=>8000)));
    }

    $query->bind_result($this->_desc);
    $query->fetch();

    $query = $this->_mysqli->prepare("SELECT `descString` FROM `trans_purchasable` WHERE `id`=?");
    $query->bind_param("i",$this->_item_id);
    $query->execute();
    $query->store_result();

    if($query->num_rows != 1){
      header("tsastatus: 500");
      exit(json_encode(array("type"=>1, "message"=>"Error Parsing Line Item", "time"=>8000)));
    }

    $query->bind_result($pd);
    $query->fetch();
    $query->close();
    $this->_desc .= " - " . $pd;

    //entrance fee
    if($this->_sku == 1){
      $this->_desc .= " [" . $this->_quantity . "] Person(s)";
    }

    //basket overide
    if($this->_chargeType == "basket"){
      $query = $this->_mysqli->prepare("SELECT `description` FROM `baskets` WHERE `ID`=?");
      $query->bind_param("i", $this->_value);
      $query->execute();
      $query->store_result();
      $query->bind_result($this->_desc);
      $query->fetch();
      $query->close();
    }

    $jString = json_encode(array(
      "sku"=>$this->_sku,
      "itemID"=>$this->_item_id,
      "desc"=>$this->_desc,
      "price"=>$this->_price
    ));
    return $jString;
  }

  //define $_price depending on $_chargeType
  private function calculateItemPrice(){
    switch($this->_chargeType){
      case "custom":
        $this->_price = $this->_value;
      break;case "leveled":
        $query = $this->_mysqli->prepare("SELECT `price` FROM `trans_purchasable` WHERE `id`=?");
        $query->bind_param("i",$this->_item_id);
        $query->execute();
        $query->store_result();
        $query->bind_result($this->_price);
		$query->fetch();
        $query->close();
      break;case "quantity":
        $this->_price = $this->_value * $this->_unitPrice;
      break;case "basket":
        $basketID = $this->_value;
        $query = $this->_mysqli->prepare("SELECT `price` FROM `baskets` WHERE `ID`=?");
        $query->bind_param("i",$basketID);
        $query->execute();
        $query->store_result();
        $query->bind_result($this->_price);
        $query->fetch();
        $query->close();
      break; default:
        header("tsastatus: 500");
        exit(json_encode(array("type"=>1, "message"=>"Calculation Error!", "time"=>8000)));
      break;
    }
  }

  public function update($itemID, $value){
    $this->_item_id = intval($itemID);
    $this->_value = intval($value);
    $this->calculateItemPrice();

    switch($this->_chargeType){
      case "custom":
        $query = $this->_mysqli->prepare("UPDATE `trans_line_items` SET `price`=? WHERE `id`=?");
        $query->bind_param("i",$this->_value);
      break;case "leveled":
        $query = $this->_mysqli->prepare("UPDATE `trans_line_items` SET `item_id`=?,`price`=? WHERE `id`=?");
        $query->bind_param("idi", $this->_item_id, $this->_price, $this->_id);
      break;case "quantity":
        $query = $this->_mysqli->prepare("UPDATE `trans_line_items` SET `quantity`=?,`price`=? WHERE `id`=?");
        $query->bind_param("idi", $this->_value, $this->_price, $this->_id);
      break;case "basket":
        $query = $this->_mysqli->prepare("UPDATE `trans_line_items` SET `quantity`=?, price=? WHERE `id`=?");
        $query->bind_param("idi", $this->_value, $this->_price, $this->_id);
        //value goes into quantity field b/c only open field and is = to basket.db id
      break;default:
        header("tsastatus: 500");
        exit(json_encode(array("type"=>1, "message"=>"Processing Error!", "time"=>8000)));
      break;
    }

    $query->execute();
    $query->store_result();
    if(!$this->_mysqli->affected_rows == 1){
      header("tsastatus: 500");
      exit(json_encode(array("type"=>1, "message"=>"[LIU]Database Error!", "time"=>8000)));
    }
  }

  //
  //Insert line item into trans_line_items
  //return insert_id on sucess || -1 on failure
  public function insert(){
    $null = NULL;$one=1;
    $query = $this->_mysqli->prepare("INSERT INTO `trans_line_items` VALUES(?,?,?,?,?)");
    switch($this->_chargeType){
      case "custom":
        $query->bind_param("iiidi", $null, $this->_item_id, $one, $this->_price, $this->_parent);
      break;case "leveled":
        $query->bind_param("iiidi", $null, $this->_item_id, $one, $this->_price, $this->_parent);
      break;case "quantity":
        $query->bind_param("iiidi", $null, $this->_item_id, $this->_value, $this->_price, $this->_parent);
      break;case "basket":
        $query->bind_param("iiidi", $null, $this->_item_id, $this->_value, $this->_price, $this->_parent);
      break;default:
        return false;
      break;
    }
    $query->execute();
    $query->store_result();
    $query->fetch();
    if($this->_mysqli->affected_rows !== 1){
      header("tsastatus: 500");
      exit(json_encode(array("type"=>1, "message"=>"[LII]Database Error!", "time"=>8000)));
    }
    $this->_id = $this->_mysqli->insert_id;
  }

  //
  //Drop line item from DB
  //
  public function drop(){
    $query = $this->_mysqli->prepare("DELETE FROM `trans_line_items` WHERE `id`=?");
    $query->bind_param("i",$this->_id);
    $query->execute();
    $query->store_result();
    $query->fetch();

    if($this->_mysqli->affected_rows !== 1){
      header("tsastatus: 500");
      exit(json_encode(array("type"=>1, "message"=>"[LID]Database Error", "time"=>8000)));
    }
  }
}
?>
