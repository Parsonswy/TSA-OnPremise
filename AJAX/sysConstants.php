<?php
class Constant {
	private static $SYS_GLOBAL_IP = "http://192.168.1.50/2016/TSA/";//Path to TSA files on webserver
	
	private static $GAME_COUNT = 5;//Number of games regestered in system
	private static $GAME_0_PARAM = array("NAME"=>"Lucky You", array(
										array("DESC"=>"Lucky You - 1 for $5", "VALUE"=>5), 
										array("DESC"=>"Lucky You - 3 for $10", "VALUE"=>10)
								),"ACTIVE"=>true);
	private static $GAME_1_PARAM = array("NAME"=>"Name Your Card", array(
										array("DESC"=>"Name Your Card 1 for $20", "VALUE"=>20),
										array("DESC"=>"Name Your Card 2 for $40", "VALUE"=>40),
      									array("DESC"=>"Name Your Card 3 for $60", "VALUE"=>60)
								),"ACTIVE"=>true);
	private static $GAME_2_PARAM = array("NAME"=>"Bag of Beer", array(
										array("DESC"=>"Bag of Beer - 1 for $10", "VALUE"=>10),
										array("DESC"=>"Bag of Beer - 2 for $20", "VALUE"=>20),
      									array("DESC"=>"Bag of Beer - 3 for $30", "VALUE"=>30)
								),"ACTIVE"=>true);
    private static $GAME_3_PARAM = array("NAME"=>"Wine Barrel Card Raffle", array(
										array("DESC"=>"Wine Barrel Card Raffle - 1 for $15", "VALUE"=>15),
										array("DESC"=>"Wine Barrel Card Raffle - 2 for $30", "VALUE"=>30),
      									array("DESC"=>"Wine Barrel Card Raffle - 3 for $45", "VALUE"=>45)
								),"ACTIVE"=>true);
    private static $GAME_4_PARAM = array("NAME"=>"Mystery Mania", array(
										array("DESC"=>"Mystery Mania - 1 for $5", "VALUE"=>5),
										array("DESC"=>"Mystery Mania - 2 for $10", "VALUE"=>10),
      									array("DESC"=>"Mystery Mania - 3 for $15", "VALUE"=>15)
								),"ACTIVE"=>true);
	public static function getSYS_GLOBAL_IP(){
		return self::$SYS_GLOBAL_IP;
	}
	public static function getGAME_COUNT(){
		return self::$GAME_COUNT;
	}
	public static function getGAME_PARAM($game){
		switch($game){
		 case 0:
			return array_values(self::$GAME_0_PARAM);
		 break;case 1:
		 	return array_values(self::$GAME_1_PARAM);
		 break;case 2:
			return array_values(self::$GAME_2_PARAM);
		 break;case 3:
          	return array_values(self::$GAME_3_PARAM);
         break;case 4:
          	return array_values(self::$GAME_4_PARAM);
         default:
		 	return null;
		 break;
		}
	}
}
//echo "<pre>";
//var_dump(Constant::getGAME_PARAM(0));
//echo "</pre>";
?>