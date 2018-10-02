<?php
  //
  //  Navigation Pane for Main Application Interface
  //
  $navigation = array(
    array("display"=>"Auction Home", "loader"=>"loadHome();", "color"=>"#TODO", "node"=>"Operator_canOperate"),
    array("display"=>"Account Summary", "loader"=>"loadAccountSummary();", "color"=>"#TODO", "node"=>"Account_canView"),
    array("display"=>"Store", "loader"=>"loadStorefront();", "color"=>"#TODO", "node"=>"Transaction_canView"),
    array("display"=>"Silent Auction", "loader"=>"loadSilentAuction();", "color"=>"#TODO", "node"=>"Auction_canView"),
    array("display"=>"Cash Out", "loader"=>"loadReceipt();", "color"=>"#TODO", "node"=>"Transaction_canView")
  );
?>
