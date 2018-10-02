<?php
session_start();//Get Session Data and url variables to determine page shceme
$uuid = $_GET['uuid'];
$id = $_SESSION['id'];
if($_SESSION['transID'])
    unset($_SESSION['transID']);
$_SESSION['transID'] = md5(time() . $uuid);
if(strlen($uuid) == 6){//Verify UUID
    $mysqli = new mysqli ('127.0.0.1', 'tsa_query', 'MTPbBXUBsQYv8e4r', 'tsa');
	$query = $mysqli->query("SELECT `id` FROM `tabcards` WHERE id=$id AND uuid=$uuid");
    if($query->num_rows === 1){//Check that a scheme is preset
        $scheme = 1;
    }else{
		$mysqli->close();
        die("ERROR! Data Mismatch! Possible fruadulent login!");
    }
}elseif(strlen($uuid) == 1){//Single char for other action schemes
    switch($uuid){
////////////////////////Case Other Manager access///////////
        case 1: $scheme = 1; break;
////////////////////////Case admin manage///////////////////
        case 2: $scheme = 2; break;
//////////////////////case activate card/////////////////
        case 3: $scheme = 3; break;
/////////////////////case account closings////////////////
        case 4: $scheme = 4; break;
////////////////////default to error////////////////////
        default: exit("Invalid Action Type!"); break;
    }
}else{$mysqli->close(); exit("Invalid Card");}
?>
<!DOCTYPE html>
<html>
    <head>
        <style type="text/css">
            div.globalNav{
                width:auto;
                height:100px;
                background-color:#94B8FF;
                padding-top:35px;
            }
            div.globalScanner{
                opacity:70%
                height:80px;
                width:80px;
            }
        </style>
    </head>
    <body>
        <div class="globalNav">
            <center>
            
            </center>
        </div>
        <?php
        ///////////////////////////////
        //[default]Case 0; activate card
        //Case 1; normal login and charge
        //Case 2; Admin login, manually manage
        //Case 3;Cashout
        //
        ///////////////////////////////
        class globalData{//Class for global data / code used by two or more functions
            public $id;
            public $_uuid;
            public $_name;
            public $_balance;
            public $_transID;
            
            //global class function properties
            private $_globalValues;//Variable to for value(s) passed to a function
            public $globalTimeStamp;
            
            //properties for logger() function
            private $_loggerUuid;
            private $_loggerName;
            private $_loggerType;
			static public $loggerContents;
            private $_loggerClient;
            private $_loggerPath;
            
            //Properties for errorHandler
            private $_errorLogPath;
            
            //Properties for downloadableRam
            private $_initialID; //used for array_dif id comparison
            
            public function __construct(){
                $this->id = $_SESSION['id'];
                $this->_uuid = $_SESSION['uuid'];
                $this->_name = $_SESSION['name'];
                $this->_transID = $_SESSION['transID'];
                $this->_balance = $_SESSION['bal'];
                $this->loggerContents = array("[LOGGER]Logger Initialized");
                $this->globalTimeStamp = date("y");
            }
            
            public function downlaodableRam($operation, $ssid, $contents){//Temporary storage to pass between multiple functions
                if(!$operation >= 0 and !$operation <= 0){
                    $mysqli->query("CREATE TABLE IF NOT EXISTS ENGINE=MEMORY `downloadableRam` id INT AUTO_INCREMENT, holder text(20) NOT NULL, contents text(200) NOT NULL,");
                    switch($operation){//^Creates memory table it non existant  || switch operation to get operation to perform
                        case "store":
                            $query = $mysqli->query("SELECT `contents` FROM `downloadableRam` WHERE holder=$sid");//Check for value already under name
                            if($mysqli->num_rows($query) < 0){//if, get data to determine id later
                                $query = $mysqli->query("SELECT id FROM `downloadableRam` WHERE holder=$ssid");
                                $row = $query->fetch_assoc();
                                $this->_initialID = array();
                                foreach($row as $value){//list of id for entries already under user
                                    array_push($this->_initialID, $value);
                                }
                            }
                            $query = $mysqli->query("INSERT INTO `downloadableRam` VALUES('',
                                                                                          '$ssid',
                                                                                          '$contents')");//store
                            if($mysqli->affected_rows($query)){//check store
                                $query = $mysqli->query("SELECT id FROM `downloadableRam` WHERE holder=$ssid");
                                $row = $query->fetch_assoc();
                                return array_diff($row, $this->_initialID);//return value of submit entry for later query
                            }
                                return false;
                        break;
                    ////////////////////////////
                        case "access"://read and return entry
                            $query = $mysqli->query("SELECT * FROM `downloadableRam WHERE id=$ssid");
                            if($mysqli->affected_rows($query)){
                                $row = $query->fetch_assoc();
                                return array($row['id'], $row['holder'], $row['contents']);
                            }else
                                return "No Values Found by the ID" . $ssid;
                        break;
                    ////////////////////////////    
                        case "wipe"://delete entires under user
                            if(strlen($ssid) == 6)
                                $query = $mysqli->query("DELETE FROM `downloadableRam` WHERE holder=$ssid");
                            else
                                $query = $mysqli->query("DELETE FROM `downloadableRam` WHERE id=$ssid");
                        break;
                    //////////////////////////
                        default: //if invalid oepration specified, return exception
                            array_push($this->loggerContents, "[RAM]Minor Exception. Invalid operation.inputvalue");
                        break;
                    }
                }else{
                    array_push($this->loggerContents, "[RAM]Minor Exception. Invalid operation.inputvalueType");
                }
            }
            
            public function showData($a, $b, $c, $d, $e){//Learn what this does
                unset($this->_globalValues);//Clear Values Variable
                $this->_values = array($a, $b, $c, $d, $e);
                foreach($this->_values as $val){
                    if($val){
                        echo self::$val . "\n";
                    }else{}
                }
            }
            
            public function logger($uuid, $name, $contents){//Error / general logger
                $this->_loggerUuid = $uuid;//get function parameters
                $this->_loggerName = $name;
                $this->loggerContents = $this->loggerContents . $contents;
                //$this->_loggerClient = $client; //IP and cookie of host w/ validation codes and such
                $this->_loggerPath = "./logs/" . $this->_loggerUuid;
                if(!file_exists("./logs/" . $this->_loggerUuid))
                    mkdir("./logs/" . $this->_loggerUuid);
                $this->_name = $type . $name;//[TransCharge/CashOut/etc][nameSpecifiedByFunctionCalled]
                $this->_loggerConents = 
				"ACCOUNT[" . $this->_loggerUuid . "]
                TMSTMP" . $this->_globalTimeStamp. "]
                TXTCNT[" . $this->_loggerPath . $this->_loggerName . "]
                EVENTS[" . var_dump($this->_loggerContents) . "]
                CALLEDCONTENT[" . $contents . "]";
                $this->_loggerFile = fopen($this->_loggerPath . $this->_loggerName . ".txt", "w");
                if(!fwrite($this->_loggerFile, $this->_loggerContents)){
                    if(!fwrite($this->_loggerFile, $this->_loggerContents)){
                        fclose($this->_loggerFile);
                        array_push($this->_loggerContents, "[WARNING][LGGER]Failed to save log to file!");
                    }else{}
                }else{}
                fclose($this->_loggerFile);
                return true;
            }
            
            public function exceptionHandler($a, $e){//Handle Errors and Exceptions
                switch($a){
                //Log exception    
                    case 1:
                        if(!file_exists("./temporary"))
                            mkdir("temporary");
                        $this->_errorLogPath = fopen("./temporary" . $this->transID, w);
                        fwrite($this->_errorLogPath, $e);
                        fclose($this->_errorLogPath);
                        return true;
                    break;
                //dump to screen / kill page
                    case 2:
                        $file = fread("./temporary/" . $this->transID, filesize("./temporary/" . $this->transID));
                        die($file);
                    break;
                //error    
                    default:
                        return false;
                    break;
                }
            }
        }
        switch($scheme){
    //Case logged in charge to account
            case 1:
                if(!isset($POST['gameCharge'])){
                    echo "<center><form action='./accountManager.php?uuid=1' method='post'><table>";
                    $scandir = scandir("./gameCFG/");//return files and count directories (-2 bc ., .. returned also)
                    $count = count($scandir) - 2;
                    for($i=0; $count >= $i; $i++){
						 echo "<iframe src='./gameCFG/" . $scandir[$i + 2] ."' style='overflow:none; boder:none;'></iframe>";
                        /*@$filepath = "./gameCFG/" . $scandir[$i+2];
                        @$file = fopen($filepath, 'r');
                        echo @fread($file, filesize($filepath));
                        @fclose($file);*/
                    }
                    echo "
                        <tr>
                            <td></td>
                            <td>
                                <input type='submit' name='gameCharge' value='Charge'/>
                            </td>
                        </tr>
                        </table></form></center>
                    ";
                }
				class chargeTab extends globalData{
						private $_total;
                        private $_filesLoaded;
						private $_loggerContents;
						
                        public function __construct(){
							parent::__construct();
							$this->_loggerContents = array();
						}
                        
                        public function getData(){
                            $scandir = scandir("./gameCFG/");
                            $this->_filesLoaded = count($scandir) - 2;
                            array_push($this->_loggerContents, "[CONF]Loaded " . $this->_filesLoaded . "game configuration files");
                            $fileOn = 2;
                            for($i=0; $this->_filesLoaded >=$i; $i++){
								if($uuid === $_POST['uuid']){
									$this->_item = $scandir[$i+2] . "charge";
									$this->_balance = $_POST[$this->_item] + $this->_balance;
									$fileOn++;
								}
                            }
							$mysqli = new mysqli ('127.0.0.1', 'tsa_query', 'MTPbBXUBsQYv8e4r', 'tsa');
							$query = $mysqli->query("UPDATE `tabcards` SET balance=`$this->_total` WHERE uuid=$this->_uuid");
									if($query->affected_rows()){
										array_push($this->_loggerContents, "[PROC]Balance of " . $this->_uuid . " set to $this->_balance;");
									}else{array_push($this->_loggerContents, "[PROC]ERROR setting balance of" . $this->_uuid . "[" . $this->name . "]");}
									
                            $query = $mysqli ->query("INSERT INTO transactions VALUES('',
                                                                                      '$this->_uuid',
                                                                                      '$this->_name',
                                                                                      '$this->_loggerUuid'
                                                                                      '$this->globalTimeStamp')");
                            if($query->affected_rows()){
                                array_push($this->_loggerContents, "[PROC]Recpit entry successfully stored to server as" . $this->globalTimeStamp);  
                            }else{array_push($this->_loggerContents, "[PROC]Error submiting transaction recipt to SQL server");}
                            array_push($this->_loggerContents, "[PROC]Balance of " . $this->_uuid . "[" . $this->_name . "] set to +" . $this->_balance);
                            echo "<center><hl><h4>Event Log</h4></hl>\n";
                            foreach($this->_loggerContents as $value){
                                echo $value . "\n";
                            }
							echo "<a href='./qrGatewWay.php>Charge Another Account</a></center>";
                            //Log :: error out; ^
                            //Complete transaction  ^
                            //Complete logger events to save values to files / sql entries
                            //Throw party if it works
                            //sacrifise to gods for good fortune
                            //confetie!
							$mysqli->close();
                            die($this->_name . "was successfully charged" . $this->_total);
                        }
                    }
				if(isset($_POST['gameCharge'])){
					$secondary = new globalData;
					$main = new chargeTab;
					return $main->getData();
					$log = $main->retunLogger();
					$secondary->logger($_SESSION['uuid'], $_SESSION['name'], $log);//uuid, name, log, transacti
				}else{$dummy = 1+1;}
            break;
    //Case 2 admin login manage things
            case 2:
                class admin extends globalData{
                    
                }
            break;
    //Case 3 cash out
            case 3:
                class cashout extends globalData{
                    
                }
            break;
    //Case 4 activiate card
            case 4:
            
            break;
    //Other
            default:
        }
        ?>
        <div align="right">
            <div class="globalScanner">
                <!--Get Image of QR code-->
            </div>
        </div>
    </body>
</html>