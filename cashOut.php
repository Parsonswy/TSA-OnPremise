<?php
session_start();

$form = "
	<form action='./cashOut.php' method='GET'>
		<input type='text' name='uuid' value='' placeholder='User UUID'/>
		<input type='hidden' name='action' value='1'/>
		<input type='submit' value='Lookup'/>
	</form>
";
if($_GET['uuid']){$uuid = $_GET['uuid'];}
else{if(ISSET($_SESSION['uuid'])){$uuid = $_SESSION['uuid'];}}
$mysqli = new mysqli("localhost", "tsa_query", "MTPbBXUBsQYv8e4r", "TSA") OR die($mysql->connect_error());
$uuid = $mysqli->real_escape_string($uuid);
$query = $mysqli->query("SELECT `balance`,`name` FROM `tabcards` WHERE `uuid`=$uuid");
if($query->num_rows){
	$qturn = $query->fetch_assoc();
	if(intval($qturn['balance']) > 0){$_SESSION['bal'] = $qturn['balance'];}
	elseif(intval($qturn['balance']) < 0){$name = $qturn['name']; die("($name)Account $uuid has a negative balance! An error may have occurred!");
	}else{$name = $qturn['name']; die("($name)UUID $uuid has no charges! $query->num_rows" . $qturn['balance']);}
}else{die("UUID Not Found!<br/>" . $form);}
/////////////////////////////////////////////
//End validation
//Start Charge
/////////////////////////////////////////////
if(ISSET($uuid) && !ISSET($_GET['lkpID'])){
	$query = $mysqli->query("SELECT * FROM `tabcards` WHERE uuid=$uuid");
	if($query->num_rows){
		$qturn = $query->fetch_assoc();
		if($qturn['balance']){$Total = $qturn['balance']; $name = $qturn['name'];}else{die("Not Obtainable balance for $uuid");}
		require("./PDF/fpdf.php");
		$pdf = new FPDF();
		$pdf->AddPage();//New PDF
		$pdf->SetFont("Arial", "B", "30");//FONT: Arial, Bold, 30px
		$pdf->SetFillColor("238, 238, 238");//Light Grey Fill
		$pdf->Cell(0, 30, "    TS Auction Receipt",1,1,"L",1);//Cell "TS Auction Recipt" x,x,Left,x
		$pdf->Image("./Images/arth.jpeg",168,10,132,30,"JPEG");//Arth logo
		$pdf->SetFont("Arial", "B", "15");//Font
		$pdf->Cell(130, 10, "Total:",1,0,"C",1);
		$pdf->Cell(60, 10, "$Total",1,1,"C",1);
		$lkptime = time();
		$tramptStamp = date("M d, Y G:i");
		$query = $mysqli->query("INSERT INTO `transactions` VALUES('',
																  '$uuid',
																  'OUT - $name',
																  '$uuid" . "$lkptime.pdf',
																  '$Total',
																  '$trampStamp',
																  '$lkptime',
																  '0')");
		$query = $mysqli->query("SELECT `id` FROM `transactions` WHERE uuid=$uuid AND lkpID=$lkptime");
		if($query->num_rows){$qturn = $query->fetch_assoc(); $transID = $qturn['id'];}else{$alt++;}
		$pdf->Cell(0,0, "Click Here To Confirm", 1, 0, "C", 0, "http://" . $_SERVER["SERVER_ADDR"] . "/html/TSA/cashOut.php?complete=$transID&lkpID=$lkptime&uuid=$uuid&alternate=$alt");
		$method=$_GET['method'];
		$pdf->Cell(0,0, "$method", 1, 0, "C", 0);
		$pdf->Output("./transactions/$uuid" . "_" . "$lkptime.pdf");
		header("Location: ./transactions/$uuid" . "_" . "$lkptime.pdf");
		$mysqli->close();
		die();
	}else{die("UUID $uuid was not found in the database!");}
}elseif(ISSET($_GET['lkpID'])){$lkpID = $_GET['lkpID'];
	if(ISSET($_GET['uuid'])){$uuid = $_GET['uuid'];}else{die("No UUID submited");}
	if(ISSET($_GET['alternate'])){
		$transID = $_GET['complete'];
		$query_String = "SELECT `uuid` FROM `transactions` WHERE uuid=$uuid AND lkpID=$lkpID";
	}elseif(ISSET($_GET['complete'])){$transID = $_GET['complete'];
		$query_String = "SELECT `uuid` FROM `transactions` WHERE id=$transID AND lkpID=$lkpID";
	}else{die("Missing confirmation parameters");}
	
	$query = $mysqli->query($query_String);
	if($query->num_rows){
		$qturn = $query->fetch_assoc();
		$uuucomf = $qturn['uuid'];
		$query = $mysqli->query("UPDATE `tabcards` SET balance=0 WHERE uuid=$uuucomf");
		$query = $mysqli->query("UPDATE `tabcards` SET active=0 WHERE uuid=$uuucomf");
		$query = $mysqli->query("SELECT `name` FROM `tabcards` WHERE uuid=$uuucomf AND balance=0 AND active=0");
		if($query->num_rows){
			$qturn = $query->fetch_assoc();
			$name = $qturn['name'];
			$query = $mysqli->query("UPDATE `transactions` SET completed=1 WHERE id=$transID AND lkpID=$lkpID");
			die("Transaction completed! Have a nice night $name! Drive safe!");
		}else{die("Unable to set balance, card may still be active#Par <a href='http://" . $_SERVER["SERVER_ADDR"] . "/html/TSA/cashOut.php?complete=$transID&$lkpID&uuid=$uuucomf'>Click here to retry</a>");}
	}
}else{die("Nothing to do");}
?>