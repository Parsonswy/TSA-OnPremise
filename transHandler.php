<?php
////////////////////////////////////////////////////////////////////////
//Initialize Varibables
///////////////////////////////////////////////////////////////////////
$exceptions = array("[INFO]Exceptions array initialized");
$mysqli = new mysqli("localhost", "tsa_query", "MTPbBXUBsQYv8e4r", "tsa") or die($mysqli->connect_error);
require("./AJAX/sysConstants.php");
define("LOOP", Constant::getGAME_COUNT());//Get gamecount for looping purposes

session_start();
if(!ISSET($_POST['charge']) && !ISSET($_GET['complete'])){
	header("Location: ./accountManager.php");
	die();
}
/////////////////////////////////////////////////////////////////////
//Session validation
////////////////////////////////////////////////////////////////////
try{
	if(ISSET($_SESSION['uuid'])){}else{throw new Exception("No user specified");}
	if(ISSET($_SESSION['name'])){}else{throw new Exception("Warning! Missing data! Possible fraudulent access");}
	if(ISSET($_SESSION['id'])){}else{throw new Exception("Warning! Missing data! Possible fraudulent access.");}
}
catch(Exception $e){
	array_push($exceptions, " | [WARNING]Empty request");
	echo "<h4>The Following Error(s) Have Occured</h4>";
	foreach($exceptions as $x){
		echo $x . "</br>";
	}
	die();
}
////////////////////////////////////////////////////////////////////
//Final transaction step. Confirmed / Add values to account balance
//
//lkpID - time() of transaction start / PDF ID
//complete - boolean of weather request is completed -> send completed message thingy at end of this if statement
///////////////////////////////////////////////////////////////////
if(ISSET($_GET['lkpID']) && isset($_GET["complete"])){
	$bal = $_GET['cost']; $uuid = $_SESSION['uuid']; 
	$lkpID = $_GET['lkpID']; $comp = $_GET['complete'];
	
	//Get transaction details from database
	$query = $mysqli->query("SELECT `cost`,`completed` FROM `transactions` WHERE uuid=$uuid AND lkpID=$lkpID AND id=$comp");
	if(!$query->num_rows === 1){die("Transaction not found");}
	$row = $query->fetch_assoc();
	$charge = $row['cost'];
	$completed = $row['completed'];
	
	//Validate database numbers
	if($completed === 1){die("Transaction $lkpID for user $uuid has already been processed.");}
	
	//Charge amount to user's account
	$mysqli->query("UPDATE `tabcards` SET `balance` = `balance` + $charge WHERE uuid=$uuid");
	$mysqli->query("UPDATE `transactions` SET completed=1 WHERE uuid=$uuid AND lkpID=$lkpID");
	$mysqli->close();
	die("Translation Completed. $uuid was charged \$$charge. A log of this transaction can be found <a href='http://" . $_SERVER["SERVER_ADDR"] . "/2016/TSA/transactions/$uuid" . "_" . "$lkpID.pdf'>here</a>.");
}

///////////////////////////////////////////////////////////////////
//Get user purchases
//
////////////////////////////////////////////////////////////////////
$userData = array($_SESSION['id'], $_SESSION['uuid'], $_SESSION['name'], $_SESSION['bal']);
$cstmCmmt = $_POST['cstmComment']; 
class goldenRetriever{
	private $_charges = array();
	//
	//Calculate total to be charged
	//
	public function total(){
		GLOBAL $mysqli;
		for ($i = 0; $i < LOOP; $i++) {
			if($_POST[$i . "cost"] > 0){
				array_push($this->_charges, array($mysqli->real_escape_string($_POST[$i . "cost"])));
				
				$gameParam = Constant::getGAME_PARAM($i);//Get game parameters
				for($j = 0;$j < count($gameParam[1]); $j++){							//Loop through all possible pricing options listed in sysConstants for specified game
					if($gameParam[1][$j]["VALUE"] == $_POST[$i . "cost"]){			//Check if item charge matches value for $i PRICE[S]
						array_push($this->_charges[$i], $gameParam[1][$j]["DESC"]);	//Select corresponding item description if match
						break;																//Break out of loop because correct value found
					}
				}
			}else{
				array_push($this->_charges, 0);//Hold space so 2nd push lines up with 1rst array push
			}
		}
		if($_POST["cstm"] > 0){
			array_push($this->_charges, array(
				$mysqli->real_escape_string($_POST['cstm']),
				$mysqli->real_escape_string($_POST['cstmComment'])
			));
		}
	}
	
	public function getCharges(){
		//die(var_dump($this->_charges));
		return array_values($this->_charges);
	}
}

class secCheck{

	static protected $uuid;
	static protected $id;
	static protected $name;
	static protected $balance;
	public function __construct($uuid, $id, $name, $balance){
	//Set user properties
		$this->uuid =  $uuid;  $this->id =    $id; 
		$this->name =  $name;  $this->balance =  $balance;
	}
	
	public function lookup(){
	GLOBAL $mysqli;
		if(!ISSET($this->balance) || !ISSET($this->name) || !ISSET($this->id) || !ISSET($this->uuid)){return "!Internal error, unable to verify user data (secChedck.lookup())";}else{}
	//Query | Check 	
		$query = $mysqli->query("SELECT `balance`,`name`,`id` FROM `tabcards` WHERE uuid=$this->uuid");
		if(!$query->num_rows){return "!User not found. UUID mismatch?";}else{}
		$row = $query->fetch_assoc();
		if(!$row['id'] === $this->id){return "!Data mismatch (ID)";}
		if(!$row['name'] === $this->name){return "!Data mismatch (name)";}
		if(!$row['balance'] === $this->balance){return "!Data mismatch (balance)";}
		return true;
	}
}

class pdf{
	private $_uuid;
	private $_name;
	private $_timeStamp;
	private $_transID;
	
	public function __construct($uuid, $name){
		$this->_uuid = $uuid;
		$this->_name = $name;
		$this->_timeStamp = date("M d, Y G:i");
	}
	
	public function generate($items){
	GLOBAL $mysqli;
		require("./PDF/fpdf.php");
		$pdf = new FPDF();
		$pdf->AddPage();
		$pdf->SetFont("Arial", "B", "30");
		$pdf->SetFillColor("238, 238, 238");
		$pdf->Cell(0, 30, "    TS Auction Receipt",1,1,"L",1);
		$pdf->Image("./Images/arth.jpeg",168,10,132,30,"JPEG");	

		$pdf->SetFont("Arial", "B", "15");
		$pdf->Cell(130, 10, "Description",1,0,"C",1);
		$pdf->Cell(60, 10, "Amount",1,1,"C",1);
		$pdf->SetFont("Arial", "", 10);
		
		$sql_trans_desc_str = "";
		$sql_trans_cost_int = 0;

		for($i = 0; $i < count($items); $i++){
			if($items[$i] == 0){continue;}
				//Get descriptions for pdf / SQL LOG
				$pdf->Cell(130, 10, $items[$i][1], 1,0,"L",0);
				$sql_trans_desc_str .= $items[$i][1] . " | ";
				
				//Get/add cost for pdf / SQL log
				$pdf->Cell(60, 10, "$" . $items[$i][0] ,1,1,"C",0);
				$sql_trans_cost_int += $items[$i][0];
		}
		

		$lkptime = time();
		$sql_trans_desc_str = $mysqli->real_escape_string($sql_trans_desc_str);
		$sql_trans_cost_int = $mysqli->real_escape_string($sql_trans_cost_int);
		$mysqli->query("INSERT INTO `transactions` VALUES('',
													      '$this->_uuid',
													      '$this->_name',
													      '$sql_trans_desc_str',
													      '$sql_trans_cost_int',
				    									  '$this->_timeStamp',
														  '$lkptime',
														  '0')");
		$query = $mysqli->query("SELECT `id` FROM `transactions` WHERE lkpID=$lkptime");
		if(!$query->num_rows == 1){die("An SQL error has occurred! Please try again \n" . var_dump($sql_trans_cost_int) . "\n" . var_dump($sql_trans_desc_str));}
		$row = $query->fetch_assoc();
		$this->transID = $row['id'];
		$pdf->Cell(130, 10, "" , 1,0,"L",0);
		$pdf->Cell(60, 10, "Total: $" .$sql_trans_cost_int, 1,1,"C",0);
		$pdf->Cell(0,0, "TRANSACTION COMPLETE. CHARGED $" . $sql_trans_cost_int, 1, 0, "C", 0, "##");//Bypassed 3/18/20162:19 am -> Direct to charge completion
		$pdf->Output("./transactions/$this->_uuid" . "_" . "$lkptime.pdf");
		header("Location: http://" . $_SERVER["SERVER_ADDR"] . "/2016/TSA/transHandler.php?complete=$this->transID&lkpID=$lkptime&cost=$sql_trans_cost_int");
		$mysqli->close();
		die();
	}
}
$gr = new goldenRetriever();
$gr->total();//Kills self on fatal error
$pdf_trans_items = $gr->getCharges();
$sc = new secCheck($userData[1], $userData[0], $userData[2], $userData[3]);
if($sc->lookup() != true){die("User authentication failed! ");}
$dev = new pdf($userData[1], $userData[2]);
$dev->generate($pdf_trans_items);
?>
