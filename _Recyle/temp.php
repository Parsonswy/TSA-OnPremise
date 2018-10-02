<?php
class goldenRetriever{
	private $loop;
	private $_gameCostBuffer = array();
	private $_gameInfoBuffer = array();
	public function __construct(){
		$this->loop = Constant::getGAME_COUNT();
		
		
		$this->game1_1 = intval($mysqli->real_escape_string($_POST['1cost']));
		$this->game2_1 = intval($mysqli->real_escape_string($_POST['2cost']));
		$this->game3_1 = intval($mysqli->real_escape_string($_POST['3cost']));
		$this->game5_1 = intval($mysqli->real_escape_string($_POST['5cost']));
		$this->game6_1 = intval($mysqli->real_escape_string($_POST['6cost']));
		$this->custom1_1 = intval($mysqli->real_escape_string($_POST['cstm']));
		$this->custom1_2 = $mysqli->real_escape_string($_POST['cstmComment']);
	}
	private function retreive(){
		GLOBAL $mysqli;

		for($i=0;$i<$this->loop;$i++){
			$index = $i . "cost";
			if($_POST[$index] != 0 && $_POST[$index] != null){
				
			}
		}
		
		//Check if custom charge exists. Add to buffer if does :: skip
		if($_POST["cstm"] != 0 && $_POST["cstm"] != null){
			array_push($this->_gameCostBuffer, $mysqli->real_escape_string($_POST['cstm']));
			array_push($this->_gameInfoBuffer, $mysqli->real_escape_string($_POST["cstmComment"]));
		}
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
?>