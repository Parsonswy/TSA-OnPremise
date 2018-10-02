<?php
session_start();
if(!ISSET($_POST['charge']) && !ISSET($_GET['complete'])){
	header("Location: ./accountManager.html");
	die();
}
$pdf_trans_desc = array();
$pdf_trans_cost = array();
$exceptions = array("[INFO]Exceptions array initialized");
$mysqli = new mysqli("localhost", "tsa_query", "MTPbBXUBsQYv8e4r", "tsa") or die($mysqli->connect_error);
if(ISSET($_GET['lkpID']) && isset($_GET['complete']) && is_int(intval($_GET['complete'])) && is_int(intval($_GET['cost']))){
	$bal = $_GET['cost']; $uuid = $_SESSION['uuid']; 
	$lkpID = $_GET['lkpID']; $comp = $_GET['complete'];
	$query = $mysqli->query("SELECT `cost`,`completed` FROM `transactions` WHERE uuid=$uuid AND lkpID=$lkpID AND id=$comp");
	if(!$query->num_rows === 1){die("Transaction not found");}
	$row = $query->fetch_assoc();
	$charge = $row['cost'];
	$completed = $row['completed'];
	if($completed === 1){die("Transaction $lkpID for user $uuid has already been processed.");}
	$query = $mysqli->query("SELECT `balance` FROM `tabcards` WHERE uuid=$uuid");
	if(!$query->num_rows){die("Balance of user $uuid not found");}
	$row = $query->fetch_assoc();
	$balance = $row['balance'];
	$balance = $balance + $charge;
	$mysqli->query("UPDATE `tabcards` SET balance=$balance WHERE uuid=$uuid");
	$mysqli->query("UPDATE `transactions` SET completed=1 WHERE uuid=$uuid AND lkpID=$lkpID");
	$mysqli->close();
	session_destroy();
	die("Translation Completed. $uuid was charged $$charge. A log of this transaction can be found <a href='http://" . $_SERVER["SERVER_ADDR"] . "/html/TSA/transactions/$uuid" . "_" . "$lkpID.pdf'>here</a>.");
}
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
$userData = array($_SESSION['id'], $_SESSION['uuid'], $_SESSION['name'], $_SESSION['bal']);
$one_cost = $_POST['1cost'];
$two_cost = $_POST['2cost'];
$cstm     = $_POST['cstm'];
$cstmCmmt = $_POST['cstmComment']; 
class goldenRetriever{
	
	static public $game1_1;
//Game 1 properties
	static public $game2_1;
//Game 2 properties
	static public $game3_1;
//Game 3 properties
	//static public $game4_1; 50/50 raffle error
//Game 4 properties
	static public $game5_1;
//Game 5 properties
	static public $game6_1;
//Custom Chage properties
	static public $custom1_1;
	static public $custom1_2;
	
	public function __construct(){//Game Name, Input Count
		//Assign values properties
			GLOBAL $mysqli;
			$this->game1_1 = intval($mysqli->real_escape_string($_POST['1cost']));
			$this->game2_1 = intval($mysqli->real_escape_string($_POST['2cost']));
			$this->game3_1 = intval($mysqli->real_escape_string($_POST['3cost']));
			$this->game5_1 = intval($mysqli->real_escape_string($_POST['5cost']));
			$this->game6_1 = intval($mysqli->real_escape_string($_POST['6cost']));
			$this->custom1_1 = intval($mysqli->real_escape_string($_POST['cstm']));
			$this->custom1_2 = $mysqli->real_escape_string($_POST['cstmComment']);
	}
	
	public function validate(){
	//Check Some Data is submitted
	GLOBAL $pdf_trans_desc;
	GLOBAL $pdf_trans_cost;
	$input = 0;
		if(ISSET($this->game1_1)){$input = $input++;}else{}
		if(ISSET($this->game2_1)){$input = $input++;}else{}
		if(ISSET($this->custom1_1)){$input = $input++;}else{}
		
	//Check valid data was submitted
		if(!ISSET($input)){return "!No data submitted";}else{}
	//Validate game 1 input	
		if(ISSET($this->game1_1) && !is_int($this->game1_1)){return "!Invalid Input Received custom(" . $this->game1_1 . " )";}	
		elseif(ISSET($this->game1_1) && is_int($this->game1_1)){
			switch($this->game1_1){
				case 0:
					//Nothing todo here
				break;
				
				case 3:
					array_push($pdf_trans_desc, "  Luck O'The Irish [1 for $3]");
					array_push($pdf_trans_cost, 3);
				break;
				
				case 5:
					array_push($pdf_trans_desc, "  Luck O'The Irish [2 for $5]");
					array_push($pdf_trans_cost, 5);
				break;
				default:
					die("Form has been modified maliciously[" . $this->game1_1 . "]");
				break;
			}
		}
	//Validate game 2 input	
		if(ISSET($this->game2_1) && !is_int($this->game2_1)){die("!Invalid Input Received ". $this->game2_1 . " Is not in int");}
		elseif(ISSET($this->game2_1) && is_int($this->game2_1)){
			switch($this->game2_1){
				case 0:
					//Nothing to do here
				break;
				
				case 40:
					array_push($pdf_trans_desc, "  Name Your Card [2 for  $40]");
					array_push($pdf_trans_cost, 40);
				break;
				
				case 20:
					array_push($pdf_trans_desc, "  Name Your Card [1 for  $20]");
					array_push($pdf_trans_cost, 20);
				break;
				
				default:
					die("Form has been modified maliciously");
				break;
			}
		}
	//Validate game 3 input	
		if(ISSET($this->game3_1) && !is_int($this->game3_1)){die("!Invalid Input Received ". $this->game3_1 . " Is not in int");}
		elseif(ISSET($this->game3_1) && is_int($this->game3_1)){
			switch($this->game3_1){
				case 0:
					//Nothing to do here
				break;
				//Cost $5
				case 5:
					array_push($pdf_trans_desc, "  Wheel Of Joy [1 for $5]");
					array_push($pdf_trans_cost, 5);
				break;
				//Cost $10
				case 10:
					array_push($pdf_trans_desc, "  Game 2 [3 for $10]");
					array_push($pdf_trans_cost, 10);
				break;
				default:
					die("Form has been modified maliciously");
				break;
			}
		}
	/*Validate game 4 input	
		if(ISSET($this->game4_1) && !is_int($this->game4_1)){die("!Invalid Input Received ". $this->game4_1 . " Is not in int");}
		elseif(ISSET($this->game4_1) && is_int($this->game4_1)){
			switch($this->game4_1){
				case 0:
					//Nothing to do here
				break;
				
				case 1:
					array_push($pdf_trans_desc, "  50/50 Raffle [1 for $3]");
					array_push($pdf_trans_cost, 3);
				break;
				
				case 5:
					array_push($pdf_trans_desc, "  50/50 Raffle [6 for $5]");
					array_push($pdf_trans_cost, 5);
				break;
				default:
					die("Form has been modified maliciously");
				break;
			}
		}
	*/
	//Validate game 5 input	
		if(ISSET($this->game5_1) && !is_int($this->game5_1)){die("!Invalid Input Received ". $this->game5_1 . " Is not in int");}
		elseif(ISSET($this->game5_1) && is_int($this->game5_1)){
			switch($this->game5_1){
				case 0:
					//Nothing to do here
				break;
				
				case 5:
					array_push($pdf_trans_desc, "  100 Bottles of Beer Raffle [1 for  $5]");
					array_push($pdf_trans_cost, 5);
				break;
				
				case 20:
					array_push($pdf_trans_desc, "  100 Bottles of Beer Raffle [5 for $20]");
					array_push($pdf_trans_cost, 20);
				break;
				default:
					die("Form has been modified maliciously");
				break;
			}
		}
	//Validate game 6 input	
		if(ISSET($this->game6_1) && !is_int($this->game6_1)){die("!Invalid Input Received ". $this->game6_1 . " Is not in int");}
		elseif(ISSET($this->game6_1) && is_int($this->game6_1)){
			switch($this->game6_1){
				case 0:
					//Nothing to do here
				break;
				
				case 5:
					array_push($pdf_trans_desc, "  Mystery Mania [1 for $5]");
					array_push($pdf_trans_cost, 5);
				break;
				
				case 10:
					array_push($pdf_trans_desc, "  Mystery Mania [2 for $10]");
					array_push($pdf_trans_cost, 10);
				break;
				default:
					die("Form has been modified maliciously");
				break;
			}
		}
	//Validate comment input	
		if(ISSET($this->custom1_1) && !is_int($this->custom1_1)){return "!Invalid Input Received";}
		elseif(ISSET($this->custom1_1) && is_int($this->custom1_1)){
			if(!ISSET($this->custom1_2)){array_push($pdf_trans_desc, "  Custom transaction");}
			if(ISSET($this->custom1_2)){array_push($pdf_trans_desc, "  " . $this->custom1_2);}
			array_push($pdf_trans_cost, $this->custom1_1);
		}
	
	//If all pass return true	
		return true;
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
	
	public function generate(){
	GLOBAL $pdf_trans_desc;
	GLOBAL $pdf_trans_cost;
	GLOBAL $mysqli;
		require("./PDF/fpdf.php");
		$pdf = new FPDF();
		$pdf->AddPage();
		$pdf->SetFont("Arial", "B", "30");
		$pdf->SetFillColor("238, 238, 238");
		$pdf->Cell(0, 30, "    TS Auction Receipt",1,1,"L",1);
		$pdf->Image("./Images/arth.jpeg",168,10,132,30,"JPEG");	
		//die(var_dump($pdf_trans_desc_str));
		$pdf->SetFont("Arial", "B", "15");
		$pdf->Cell(130, 10, "Description",1,0,"C",1);
		$pdf->Cell(60, 10, "Amount",1,1,"C",1);
		$x = 0; $pdf->SetFont("Arial", "", 10);
		foreach($pdf_trans_desc as $value){
			$pdf->Cell(130, 10, $value, 1,0,"L",0);
			$pdf->Cell(60, 10, "$" . $pdf_trans_cost[$x] ,1,1,"C",0);
			$x++;
		}
		$sql_trans_desc_str = "";
		$sql_trans_cost_int = 0;
		foreach($pdf_trans_desc as $value){
			$sql_trans_desc_str = $sql_trans_desc_str . $value . " | ";
		}
		if(strlen($sql_trans_desc_str) <= 4){die("Null Transaction.");}
		foreach($pdf_trans_cost as $value){
			$sql_trans_cost_int = $sql_trans_cost_int + $value;
		}
		if($sql_trans_cost_int <= 0){die("Null transaction.");}
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
		$pdf->Cell(0,0, "Click Here To Confirm", 1, 0, "C", 0, "http://" . $_SERVER["SERVER_ADDR"] . "/html/TSA/transHandler.php?complete=$this->transID&lkpID=$lkptime&cost=$sql_trans_cost_int");
		$pdf->Output("./transactions/$this->_uuid" . "_" . "$lkptime.pdf");
		header("Location: ./transactions/$this->_uuid" . "_" . "$lkptime" . ".pdf");
		$mysqli->close();
		die();
	}
}
$gr = new goldenRetriever();
$gr->validate();//Kills self on fatal error
$sc = new secCheck($userData[1], $userData[0], $userData[2], $userData[3]);
if(strpos($sc->lookup(), "!")){die("User authentication failed! Account does not appear to exist.");}
$dev = new pdf($userData[1], $userData[2]);
$dev->generate();
?>
