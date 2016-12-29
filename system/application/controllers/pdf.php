<?php
require ('fpdf.php');

class PDF extends FPDF {
	/*
	  * changed from Fancy Table Function
	  * of examples FPDF
	  * http://localhost/fpdf/tutorial/tuto5.htm
	  * into Report_Table_Station_4
	  */
	function Report_Table_Station_4($header,$data,$saldo) {
		//Colors, line width and bold font
		$this->SetFillColor(217,217,255);
		$this->SetTextColor(0);
		$this->SetDrawColor(232,232,232);
		$this->SetLineWidth(.3);
		$this->SetFont('','B');
		//Header
		$w=array(10,30,25,20,30,30,25);
		for($i=0;$i<count($header);$i++)
			$this->Cell($w[$i],7,$header[$i],1,0,'C',true);
		$this->Ln();
		//Color and font restoration
		$this->SetFillColor(240,240,240);
		$this->SetTextColor(0);
		$this->SetFont('');
		//Data
		$fill=false;
		$i = 1;
		foreach($data as $row)
		{
			
			//$saldo -= $row->amount;
			$this->Cell($w[0],6,$i,'LR',0,'R',$fill);
			$this->Cell($w[1],6,$row->mr_no,'LR',0,'C',$fill);
			$this->Cell($w[2],6,$row->operation_date,'LR',0,'L',$fill);
			$this->Cell($w[3],6,$row->amount,'LR',0,'R',$fill);
			$this->Cell($w[4],6, "Rp. ".number_format($row->price, 0, ',', '.'),'LR',0,'R',$fill);
			$this->Cell($w[5],6,"Rp. ".number_format(($row->price * $row->amount), 0, ',', '.'),'LR',0,'R',$fill);
			$this->Cell($w[6],6,$row->sisa,'LR',0,'R',$fill);
			$this->Ln();
			$fill=!$fill;
			$i++;
		}
		$this->Cell(array_sum($w),0,'','T');
	}
	
	function AngsamerahLogo() {
		$this->Image("http://localhost/img/angsamerah.png",25,10,82.9,25.4);
		$this->SetXY(10,40);
	}
}