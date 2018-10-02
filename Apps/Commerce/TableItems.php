<?php
abstract class TableItems{
  CONST ENTRY_FEE = 30;
  CONST GAME_COUNT = 1;
  CONST GAME_0_PARAM = array("NAME"=>"Lucky You - Speaker", "OPTS"=>array(
    					array("DESC"=>"1 for $10 - Lucky You", "VALUE"=>10),
    					array("DESC"=>"2 for $20 - Lucky You", "VALUE"=>20),
                        array("DESC"=>"3 for $30 - Lucky You", "VALUE"=>30)
                      ),"ACTIVE"=>true);
  CONST GAME_1_PARAM = array("NAME"=>"Name Your Card - Paddle Board", "OPTS"=>array(
    					array("DESC"=>"1 for $10 Name Your Card", "VALUE"=>10),
    					array("DESC"=>"2 for $20 Name Your Card", "VALUE"=>20),
                        array("DESC"=>"3 for $30 - Name Your Card", "VALUE"=>30)
                      ),"ACTIVE"=>true);
  CONST GAME_2_PARAM = array("NAME"=>"Card Raffle - Washer", "OPTS"=>array(
                        array("DESC"=>"1 for $10 - Card Raffle", "VALUE"=>10),
                        array("DESC"=>"2 for $20 - Card Raffle", "VALUE"=>20),
                        array("DESC"=>"3 for $30 - Card Raffle", "VALUE"=>30)
                      ),"ACTIVE"=>true);
  CONST GAME_3_PARAM = array("NAME"=>"100 Bottles of Beer", "OPTS"=>array(
                        array("DESC"=>"1 for $5 - 100 Bottles of Beer", "VALUE"=>5),
                        array("DESC"=>"2 for $10 - 100 Bottles of Beer", "VALUE"=>10),
                        array("DESC"=>"3 for $15 - 100 Bottles of Beer", "VALUE"=>15)
                      ),"ACTIVE"=>true);
  CONST GAME_4_PARAM = array("NAME"=>"Mystery Mania", "OPTS"=>array(
                        array("DESC"=>"1 for $5 - Mystery Mania", "VALUE"=>5),
                        array("DESC"=>"2 for $10 - Mystery Mania", "VALUE"=>10),
                        array("DESC"=>"3 for $15 - Mystery Mania", "VALUE"=>15)
                      ),"ACTIVE"=>true);
  CONST GAME_5_PARAM = array("NAME"=>"Team Savannah Hats", "OPTS"=>array(
                        array("DESC"=>"1 for $15 - Hats", "VALUE"=>15),
                        array("DESC"=>"2 for $30 - Hats", "VALUE"=>30),
                        array("DESC"=>"3 for $45 - Hats", "VALUE"=>45)
                      ),"ACTIVE"=>true);
  //Used for looping through all games
  CONST GAME_LIST = array(SELF::GAME_0_PARAM,SELF::GAME_1_PARAM,SELF::GAME_2_PARAM,SELF::GAME_3_PARAM,SELF::GAME_4_PARAM, SELF::GAME_5_PARAM);
}
?>
