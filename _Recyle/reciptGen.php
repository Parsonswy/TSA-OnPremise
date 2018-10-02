<?php
	require("./PDF/fpdf.php");
	$pdf = new FPDF();
	$pdf->AddPage();//New PDF
	$pdf->SetFont("Arial", "B", "30");//FONT: Arial, Bold, 30px
	$pdf->SetFillColor("238, 238, 238");//Light Grey Fill
	$pdf->Cell(0, 30, "    TS Auction Receipt",1,1,"L",1);//Cell "TS Auction Recipt" x,x,Left,x
	$pdf->Image("./Images/arth.jpeg",168,10,132,30,"JPEG");//Arth logo
	$pdf->SetFont("Arial", "", "12");//Font
	$pdf->SetFillColor("255, 255, 255");//Light Grey Fill
	$pdf->Cell(130, 10, "Luck O' The Irish [2 for $5]:",1,0,"C",1);
	$pdf->Cell(60, 10, "$5",1,1,"C",1);
	$pdf->Cell(130, 10, "Name Your Card [1 for $20]:",1,0,"C",1);
	$pdf->Cell(60, 10, "$20",1,1,"C",1);
	$pdf->Cell(130, 10, "100 Bottles of Beer Raffle [5 for $20]:",1,0,"C",1);
	$pdf->Cell(60, 10, "$20",1,1,"C",1);
	$pdf->Cell(130, 10, "Luck O' The Irish [2 for $5]:",1,0,"C",1);
	$pdf->Cell(60, 10, "$5",1,1,"C",1);
	$pdf->Cell(130, 10, "Basket 31[Rainy Day Unplugged]",1,0,"C",1);
	$pdf->Cell(60, 10, "$45",1,1,"C",1);
	$pdf->Cell(130, 10, "Basket 44[Avalanche Bay]:",1,0,"C",1);
	$pdf->Cell(60, 10, "$80",1,1,"C",1);
	$pdf->SetFillColor("238, 238, 238");//Light Grey Fill
	$pdf->SetFont("Arial", "B", "15");//Font
	$pdf->Cell(130, 10, "Total:",1,0,"C",1);
	$pdf->Cell(60, 10, "$175",1,1,"C",1);
	$pdf->Output("./Mike_Lesley_Nelson.pdf");
?>