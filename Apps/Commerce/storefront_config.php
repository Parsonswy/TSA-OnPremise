<?php
  //Would be generated when management updates trans_purchasable / trans_stock tables
  function getStorefront(){
    return array(
            array(
              "display"=>"Lucky You - Speaker",
              "item"=>3,
              "img"=>"https://172.16.8.242/Development\Apps\Accounts\Accounts\\273549.png",
              "payType"=>"count",
              "price"=>5,
              "id"=>3
            ),

            array(
              "display"=>"Name Your Card - Corn Hole",
              "item"=>4,
              "img"=>"https://172.16.8.242/Development\Apps\Accounts\Accounts\\273549.png",
              "payType"=>"count",
              "price"=>10,
              "id"=>4
            ),

            array(
              "display"=>"Card Raffle - Brew-Ha-Ha",
              "item"=>5,
              "img"=>"https://172.16.8.242/Development\Apps\Accounts\Accounts\\273549.png",
              "payType"=>"count",
              "price"=>10,
              "id"=>5
            ),

            array(
              "display"=>"Cooler of Libations - Raffle",
              "item"=>6,
              "img"=>"https://172.16.8.242/Development\Apps\Accounts\Accounts\\273549.png",
              "payType"=>"drop",
              "payLevels"=>array(
                array("desc"=>"2 For $10.00", "price"=>10, "id"=>6),
                array("desc"=>"5 For $20.00 ", "price"=>20, "id"=>7)
              )
            ),

            array(
              "display"=>"Mystery Mania",
              "item"=>7,
              "img"=>"https://172.16.8.242/Development\Apps\Accounts\Accounts\\273549.png",
              "payType"=>"count",
              "price"=>5,
              "id"=>8
            ),

            array(
              "display"=>"Team Savannah Gear",
              "item"=>8,
              "img"=>"https://172.16.8.242/Development\Apps\Accounts\Accounts\\273549.png",
              "payType"=>"drop",
              "payLevels"=>array(
                array("desc"=>"Button -  $1.00", "price"=>1, "id"=>9),
                array("desc"=>"Hat - $10.00 ", "price"=>10, "id"=>10)
              )
            )
            // array(//:0
            //   "display"=>"Mystery Machine",
            //   "item"=>2,
            //   "img"=>"https://172.16.8.242/Development\Apps\Accounts\Accounts\\724433.png",
            //   "payType"=>"drop",
            //   "payLevels"=>array(
            //     array("desc"=>"1 for $20,000", "price"=>25000, "id"=>4),
            //     array("desc"=>"2 for $35,000", "price"=>35000, "id"=>5)
            //   )
            // ),

            /*array(//:1
              "display"=>"Scooby Snacks"
              "item"=>NULL,
              "img"=>"https://172.16.8.242/Development\Apps\Accounts\Accounts\\273549.png",
              "payType"=>"radio",
              "payLevels"=>array(
                array("desc"=>"Two for $3", "price"=>3),
                array("desc"=>"Three for $5", "price"=>5)
              )
            ),*/

            // array(//:2
            //   "display"=>"Fruit Snacks",
            //   "item"=>4,
            //   "img"=>"https://172.16.8.242/Development\Apps\Accounts\Accounts\\273549.png",
            //   "payType"=>"custom",
            //   "id"=>7
            // ),


          );
  }
?>
