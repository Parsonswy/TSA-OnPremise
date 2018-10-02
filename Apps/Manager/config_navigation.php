<?php
//
//  Navigation Pane for Managment Interface
//
$navigation = array(
  array("display"=>"Management Home", "loader"=>"loadHome();", "color"=>"#TODO", "node"=>"Manage_canView"),
  array("display"=>"Operator Configuration", "loader"=>"loadOperatorCFG();", "color"=>"#TODO", "node"=>"Manage_canOperator"),
  array("display"=>"Permissions Manager", "loader"=>"loadPermissionsCFG();", "color"=>"#TODO", "node"=>"Manage_canPermission"),
  array("display"=>"Statistics", "loader"=>"loadStatistics();", "color"=>"#TODO", "node"=>"Manage_viewStats")
);
?>
