<?php

class Station_5 extends Controller {

	function Station_5()
	{
		parent::Controller();
		$this->load->helper('url');
		$this->load->helper('form');
		$this->load->library('session');		
		if ($this->session->userdata('logged_in') != TRUE || $this->session->userdata('station') != '5') {
				redirect('/welcome');
		}
		//$this->load->model('appointment','',TRUE);
		
	}
	
	
	function index()
	{
		
		$data['baseURL'] = base_url();
		$data['title'] = "Control Panel Station ".$this->session->userdata('station')." - ".$this->session->userdata('name');
		$data['custom_message'] = "<h2>Welcome to Control Panel Station ".$this->session->userdata('station')."</h2><br />Please select the operation you need in the menu to your left.";		
		$this->load->view('custom_message',$data);
	}
	
	
	//---- ATTENTION!!
	// THIS paid_item FUNCTION WILL BE MOVED TO STATION 2 *WRONG: 3* EXAMINATION ROOM, STATION 2
		
	
	
	function patient_billing() {
		$this->load->model('payment','',TRUE);
		$data['baseURL'] = base_url();
		$data['menu_5_patient_billing'] = TRUE;
		// FETCH THE DATA OF PATIENT NOT YET EXAMINED
		// PARAMETER USING 'N' FOR THE FUNCTION GET PATIENT EXAMINED
		$data['result'] = $this->payment->get_patient_examined('Y');//print_r($data);exit;
		$this->load->view('station_5_patient_billing',$data);
	}
	
	function demo_invoice() {
		//$req = "../..fpdf.php";
		require "../../inc/invoice.php";
		
		$inv = new Invoice();
		$inv->AddPage();
		$inv->Output();
	}
	
	function report() {
		$data['baseURL'] = base_url();
		$data['report_station5'] = TRUE;
		$data['no_nav'] = TRUE;
		$this->load->view('report_station_5',$data);
	}
	
	function generate_report($month='',$year='') {
		// THERE ARE TWO TYPES OF REPORT THAT CAN BE PRODUCED: DAILY OR MONTHLY
		// THE DAILY USING THE VARIABLE $_POST['report_date'] 
		// THE MONTHLY USING THE PARAMETERS $month AND $year
		
		// FETCHING REPORT DATA
		$this->load->model('payment','',TRUE);
		$type = 'M';
		$param1 = $month;
		$param2 = $year;
		if (isset($_POST['report_date'])) {
			$type = 'D';
			$param1 = $_POST['report_date'];
		}
		$data = $this->payment->fetch_payment($type,$param1,$param2);
		if (!empty($data)) {
			$title = "Patient Billing Report ";
			if ( $type == 'M' )
				$title .= $this->month_name($param1)." $param2";
			else
				$title .= $this->long_format_date($param1);
			echo "<form method=\"post\" action=\"". site_url("general/export_report") ."\">";
			echo "<input type=\"submit\" value=\"Download report\" />";
			echo "<h3>$title</h3>";
			echo "<table class=\"report\" border=\"1\">";
			// TABLE HEADER
			echo "	<tr>";
			echo "		<th rowspan='2'>No.</th>";
			echo "		<th rowspan='2'>Date</th>";
			echo "		<th rowspan='2'>Receipt #</th>";
			echo "		<th rowspan='2'>Name</th>";
			echo "		<th rowspan='2'>Status</th>";
			// add two more fields: doctor and nurse Feb 18, 2011
			echo "		<th rowspan='2'>Doctor</th>";
			echo "		<th rowspan='2'>Nurse</th>";
			// end of addition
			// add two more fields: service procedure and Package fee March 03, 2011
			echo "		<th rowspan='2'>Service Proc</th>";
			echo "		<th rowspan='2'>AMC Package Fee</th>";
			echo "		<th rowspan='2'>Paramita Fee</th>";
			// end of addition
			echo "		<th rowspan='2'>Admin Fee</th>";
			echo "		<th rowspan='2'>Consult Fee</th>";
			echo "		<th rowspan='2'>Lab Fee</th>";
			echo "		<th rowspan='2'>Proc Fee</th>";
			echo "		<th rowspan='2'>Medicine Fee</th>";
			// addition July 20, 2011 - discount field
			echo "		<th rowspan='2'>Discount</th>";
			// end of addition			
			echo "		<th rowspan='2'>Total</th>";
			echo "		<th rowspan='2'>Date Paid</th>";
			echo "		<th rowspan='2'>Ket.</th>";
			// addition for print receipt 4 / 8 / 2011
			echo "		<th colspan='2'>Print Receipt with Details</th>";
			echo "	</tr>";
			echo "	<tr>";
			echo "		<th>Yes</th>";
			echo "		<th>No</th>";
			echo "	</tr>";
			// end of addition
			// END OF TABLE HEADER
			
			
			
			// =========== table report header ======//
			$body_report = "";
			$body_report .= "<table>";
			// TABLE HEADER
			$body_report .= "	<tr>";
			$body_report .= "		<th>No.</th>";
			$body_report .= "		<th>Date</th>";
			$body_report .= "		<th>Receipt #</th>";
			$body_report .= "		<th>Name</th>";
			$body_report .= "		<th>Status</th>";
			// add two more fields: doctor and nurse Feb 18, 2011
			$body_report .= "		<th>Doctor</th>";
			$body_report .= "		<th>Nurse</th>";
			// end of addition
			// add two more fields: service procedure and Package fee March 03, 2011
			$body_report .= "		<th>Service Proc</th>";
			$body_report .= "		<th>AMC Package Fee</th>";
			$body_report .= "		<th>Paramita Fee</th>";
			// end of addition
			$body_report .= "		<th>Admin Fee</th>";
			$body_report .= "		<th>Consult Fee</th>";
			$body_report .= "		<th>Lab Fee</th>";
			$body_report .= "		<th>Proc Fee</th>";
			$body_report .= "		<th>Medicine Fee</th>";
			// addition July 20, 2011 - discount field
			$body_report .= "		<th>Discount</th>";
			// end of addition			
			$body_report .= "		<th>Total</th>";
			$body_report .= "		<th>Date Paid</th>";
			$body_report .= "		<th>Keterangan</th>";
			$body_report .= "	</tr>";
			// ===== END OF TABLE HEADER REPORT ==== /
			
			
			
			//print_r($data);exit;
			// TABLE BODY
			$i = 1;
			$class = "odd";
			$style = "background:#f0f0f0"; // add this for report download
			$total_admin = 0;
			$total_doctor = 0;
			$total_lab = 0;
			$total_proc = 0;
			$total_med = 0;
			$total_package_fee = 0;
			$total_paramita_fee = 0;
			$total_diskon = 0;
			
			// additional fee: package fee
			foreach ($data as $row) {
				echo "	<tr class=\"$class\" >";
				echo "		<td>$i</td>";
				echo "		<td>".$this->long_format_date($row->app_date)."</td>";
				echo "		<td>$row->receipt_no</td>";
				echo "		<td>$row->nickname</td>";
				echo "		<td>$row->status</td>";
				// add two more fields: doctor and nurse Feb 18, 2011
				echo "		<td>$row->doctor_name".($row->other_doctor_name==""?"":" ($row->other_doctor_name)")."</td>";
				echo "		<td>$row->nurse_name</td>";
				// end of addition
				// add two more fields: service procedure and Package fee March 03, 2011
				echo "		<td>$row->service_procedure</td>";
				echo "		<td style=\"text-align:right\">".$this->IDformat($row->amc_package_fee)."</td>";
				echo "		<td style=\"text-align:right\">".$this->IDformat($row->paramita_fee)."</td>";
				// end of addition
				echo "		<td style=\"text-align:right\">".$this->IDformat($row->administration_fee)."</td>";
				echo "		<td style=\"text-align:right\">".$this->IDformat($row->doctor_fee)."</td>";
				echo "		<td style=\"text-align:right\">".$this->IDformat($row->lab_fee)."</td>";
				echo "		<td style=\"text-align:right\">".$this->IDformat($row->proc_fee)."</td>";
				echo "		<td style=\"text-align:right\">".$this->IDformat($row->med_fee)."</td>";
				// addition July 20, 2011 - discount field
				echo "		<td style=\"text-align:right\">".($row->disc_percentage > 0 ? ($this->IDformat($row->disc_amount)." ($row->disc_percentage%)") : "-")."</td>";
				// end of addition			
				// amount added with "- discount"
				echo "		<td style=\"text-align:right\">".$this->IDformat($row->administration_fee + $row->doctor_fee + $row->lab_fee + $row->proc_fee + $row->med_fee + $row->amc_package_fee + $row->paramita_fee - $row->disc_amount)."</td>";
				echo "		<td>".$this->long_format_date($row->date)."</td>";
				echo "		<td>$row->payment_method</td>";
				echo "		<td><a href ='".site_url("station_5/print_invoice/$row->appointment_id/R/1")."' target='_blank'>With Details</a></td>";
				echo "		<td><a href='".site_url("station_5/print_invoice/$row->appointment_id/R")."' target='_blank'>No Details</a></td>";
				echo "	</tr>";
				
				// --- report body ---//
				$body_report .= "	<tr style='$style'>";
				$body_report .= "		<td>$i</td>";
				$body_report .= "		<td>$row->app_date</td>";
				$body_report .= "		<td>$row->receipt_no</td>";
				$body_report .= "		<td>$row->nickname</td>";
				$body_report .= "		<td>$row->status</td>";
				// add two more fields: doctor and nurse Feb 18, 2011
				$body_report .= "		<td>$row->doctor_name".($row->other_doctor_name==""?"":" ($row->other_doctor_name)")."</td>";
				$body_report .= "		<td>$row->nurse_name</td>";
				// end of addition
				// add two more fields: service procedure and Package fee March 03, 2011
				$body_report .= "		<td style='text-align:right'>$row->service_procedure</td>";
				$body_report .= "		<td style='text-align:right'>$row->amc_package_fee</td>";
				$body_report .= "		<td style='text-align:right'>$row->paramita_fee</td>";
				// end of addition
				$body_report .= "		<td style='text-align:right'>".($row->administration_fee)."</td>";
				$body_report .= "		<td style='text-align:right'>".($row->doctor_fee)."</td>";
				$body_report .= "		<td style='text-align:right'>".($row->lab_fee)."</td>";
				$body_report .= "		<td style='text-align:right'>".($row->proc_fee)."</td>";
				$body_report .= "		<td style='text-align:right'>".($row->med_fee)."</td>";
				// addition July 20, 2011 - discount field
				$body_report .= "		<td style='text-align:right'>".($row->disc_amount)."</td>";
			// end of addition	
				$body_report .= "		<td style='text-align:right'>".($row->administration_fee + $row->doctor_fee + $row->lab_fee + $row->proc_fee + $row->med_fee + $row->amc_package_fee + $row->paramita_fee - $row->disc_amount)."</td>";
				$body_report .= "		<td>$row->date</td>";
				$body_report .= "		<td>$row->payment_method</td>";
				$body_report .= "	</tr>";
				// --- report body ends --//
				
				$total_admin += $row->administration_fee;
				$total_doctor += $row->doctor_fee;
				$total_lab += $row->lab_fee;
				$total_proc += $row->proc_fee;
				$total_med += $row->med_fee;
				$total_package_fee += $row->amc_package_fee;
				$total_paramita_fee += $row->paramita_fee;
				$total_diskon += $row->disc_amount;
				$class == "odd" ? $class = "even" : $class = "odd";
				
				// add this for report download
				$style == "background:#f0f0f0" ? $style = "background:#ffffff" : $style = "background:#f0f0f0";
				
				++$i;
			}
			echo "	<tr class=\"$class\" >";
			// colspan is 7 MARCH 3,2011
			// colspan is 8: march 3, 2011
			echo "		<td colspan=\"8\">T O T A L</td>";
			echo "		<td style=\"text-align:right\">".$this->IDformat($total_package_fee)."</td>";
			echo "		<td style=\"text-align:right\">".$this->IDformat($total_paramita_fee)."</td>";
			echo "		<td style=\"text-align:right\">".$this->IDformat($total_admin)."</td>";
			echo "		<td style=\"text-align:right\">".$this->IDformat($total_doctor)."</td>";
			echo "		<td style=\"text-align:right\">".$this->IDformat($total_lab)."</td>";
			echo "		<td style=\"text-align:right\">".$this->IDformat($total_proc)."</td>";
			echo "		<td style=\"text-align:right\">".$this->IDformat($total_med)."</td>";
			echo "		<td style=\"text-align:right\">".$this->IDformat($total_diskon)."</td>";
			echo "		<td style=\"text-align:right\">". $this->IDformat($total_admin + $total_doctor + $total_lab + $total_proc + $total_med + $total_package_fee + $total_paramita_fee - $total_diskon )."</td>";
			echo "		<td>&nbsp;</td>";
			echo "		<td>&nbsp;</td>";
			echo "	</tr>";
			echo "</table>";			
			
			$body_report .= "	<tr style='$style' >";
			$body_report .= "		<td colspan='8'>T O T A L</td>";
			$body_report .= "		<td style='text-align:right'>$total_package_fee</td>";
			$body_report .= "		<td style='text-align:right'>$total_paramita_fee</td>";
			$body_report .= "		<td style='text-align:right'>".$total_admin."</td>";
			$body_report .= "		<td style='text-align:right'>".($total_doctor)."</td>";
			$body_report .= "		<td style='text-align:right'>".($total_lab)."</td>";
			$body_report .= "		<td style='text-align:right'>".($total_proc)."</td>";
			$body_report .= "		<td style='text-align:right'>".($total_med)."</td>";
			// addition July 20, 2011 - discount field
			$body_report .= "		<td style='text-align:right'>".($total_diskon)."</td>";
			// end of addition	
			$body_report .= "		<td style='text-align:right'>". ($total_admin + $total_doctor + $total_lab + $total_proc + $total_med + $total_package_fee + $total_paramita_fee - $total_diskon)."</td>";
			$body_report .= "		<td>&nbsp;</td>";
			$body_report .= "		<td>&nbsp;</td>";
			$body_report .= "	</tr>";
			$body_report .= "</table>";
			//$data['body_report']			
			echo "<input type=\"hidden\" value=\"$body_report\" name=\"body_report\" />";
			echo "<input type=\"hidden\" value=\"$title\" name=\"title_report\" />";
			echo "</form>";
			//echo $body_report;
			
		} else {
			echo "<h3 style='color:red'>No report for ";
			if ( $type == 'M' )
				echo ($this->month_name($param1)." $param2</h3>");
			else
				echo $this->long_format_date($param1)."</h3>";
		}
		//echo "Success";
	}
	
	function payment($app_id) {
		$this->load->model('payment','',TRUE);
		$data['details'] = $this->payment->get_payment_details($app_id);
		$data['app_date'] = $this->long_format_date($data['details']->appointment_date);
		$data['baseURL'] = base_url();
		$data['payment_station5'] = TRUE;
		$data['app_id'] = $app_id;
		$data['digit_group'] = TRUE;
		$data['payment_method'] = $this->payment->load_payment_method();
		$data['extra_msg'] = "<div class='success'>Transaction has been processed.</div>";
		$data['menu_5_patient_billing'] = TRUE;
		//print_r($data);exit;
		$this->load->view('station_5_payment',$data);
	}
	
	// the function to print invoice
	// type = B means 'billing' => no receipt number and header BILLING <-- needs confirmation with angsamerah -DONE
	// type = R means RECEIPT=> receipt number, header "RECEIPT" and save the data to table tb_payment
	function print_invoice($app_id,$type='B',$details=0) {
		$this->load->model('payment','',TRUE);
		
		$receipt_no = "";
		// IF IT'S A PAYMENT WHICH TYPE 'R'
		// SAVE ALL THE DATA TO tb_payment
		if ($type == 'R' && isset($_POST['payment_date']) && !($this->payment->check_paid_status($app_id)) ) {
			//print_r($_POST);exit;
			// this is to save the payment amount, type and etc
			// to tb_payment
			$this->payment->save_payment($_POST);
			$receipt_no = $this->payment->generate_no_receipt($app_id);
		}
		
		//print_r($_POST);exit;
		require "fpdf.php";
		
		// THE ADDRESS POSITION ON TOP RIGHT OF THE INVOICE
		// THE TEXT IS "Angsamerah Clinic"
		// "Graha Media Building 2nd Floor"
		// Jl. Blora no. 8-10 Menteng - Jakarta Pusat
		// Phone: +6221 3915189
		// $add_x is for the x coordinate; $add_y is for the y coordinate of the address
		// starting point is x = 150 mm; updated Friday 30 Oct 2010 x = 130 mm and y = 10 mm
		$add_x = 130;
		$add_y = 10;
		
		// default of the left margin
		$curr_x = 25;
		$curr_y;
		
		$item_font_size = 12;
		$footer_font_size = 9;
		$rp_x = 115; // the margin for "Rp." from the curr_x
		
		// for the Write() height parameter, so all will have the same value
		$h_wr = 13;
		$baseURL = base_url(); 
		
		if ($type == 'R') $invoice_header = "RECEIPT";
		else $invoice_header = "BILLING";
		
		
		$price = $this->payment->get_payment_details($app_id);
		//print_r($price);exit;
		// getting the value to variable
		$admin_fee = $price->administration_fee; //$_POST['price_admin'];  // the price for admin
		$doctor_fee = $price->doctor_fee; //$_POST['price_doctor'];;  // the price for doctor
		$medicine_fee = $this->payment->get_jumlah_harga_obat($app_id); //$_POST['price_obat'];  // the price for medicine
		$proc_fee = $price->proc_fee; //$_POST['price_tindakan'];  // the price for procedure
		$lab_fee = $price->lab_fee; //$_POST['price_lab'];   // the price for lab
		$package_fee = $price->amc_package_fee;
		$payment_method = 1;  // 1 - tunai 2 = credit card ; 3 = debit card// $_POST['payment_method'];
		$total_payment = $admin_fee + $doctor_fee + $medicine_fee + $proc_fee + $lab_fee + $package_fee; //$_POST['grand_total'];  // the grand total
		
		// PAY ATTENTION TO THIS!
		// THIS HAS TO BE THE RIGHT SALUTATION AND FULL NAME
		$salutation = $price->salutation;
		$full_name = trim("$price->full_name")==""? $price->nickname : $price->full_name;
		//echo($full_name);exit;
		
		$pay_details = $this->payment->get_payment_method_patient($app_id);
		if ($type == 'R') $date = $this->long_format_date($pay_details->date);
		else $date = date("F d, Y");
		
		
		
		// PAY ATTENTION TO RECEIPT NO
		// RECEIPT NUMBER IS ONLY IF PAYMENT IS MADE
		//$receipt_no = $no_receipt;//"020/AMC/X-11";
		
		// FPDF constructor paramters: L = landscape, mm = milimeters (unit used in the document), letter (the size of the paper used in the document)
		$laporan = new FPDF();
		$laporan->AddPage();
		
		// image place coordinate (35,10) image size 82.9 mm (235 px) x 25.4 mm (72px)
		// UPDATE: image place coordinate is according variable $curr_x
		$laporan->Image($baseURL."img/angsamerah.png",$curr_x,10,82.9,25.4);
		
		// SetFont(fontfamily, style (bold, underline, italic), size); if we don't want to use style just leave the 2nd parameter blank.
		$laporan->SetFont('helvetica','B',9);
		
		
		/*$laporan->Cell(180,10,'Angsamerah Clinic',0,1,'R');
		$laporan->SetFont('','');*/
		
		//starts writing the address header
		$laporan->SetXY($add_x,$add_y);
		$laporan->Write($h_wr,"Angsamerah Clinic");
		$laporan->SetFont('',''); // set font into no style mode (no bold, underline nor italic)
		$add_y += 5;
		$laporan->SetXY($add_x,$add_y);
		$laporan->Write($h_wr,"Graha Media Building 2nd Floor");
		$add_y += 5;
		$laporan->SetXY($add_x,$add_y);
		$laporan->Write($h_wr,"Jl. Blora no. 8-10 Menteng - Jakarta Pusat");
		$add_y += 5;
		$laporan->SetXY($add_x,$add_y);
		$laporan->Write($h_wr,"Phone: +6221 3915189");
		
		$h_wr = 11;
		// give 10 mm for the next 'To' & 'No' of invoice
		$curr_y = $add_y + 10;
		$laporan->SetFont('','',12);
		$laporan->SetXY($curr_x, $curr_y);
		$laporan->Write($h_wr,"To: ". $salutation ." ". $full_name );
		$curr_y += 6;
		$laporan->SetXY($curr_x, $curr_y);
		$laporan->SetFont('','',10);
		
		// ONLY WRITE THE RECEIPT NO ONLY FOR 'RECEIPT' TYPE
		if ($type == 'R') {
			if ($receipt_no == "")
				$receipt_no = $pay_details->receipt_no;
			$laporan->Write($h_wr,"No: ".$receipt_no);
		}
		
		// writing the place & date
		$laporan->SetXY($add_x, $curr_y);
		$laporan->Write($h_wr,"Jakarta, ".$date);
		
		// WRITING THE INVOICE TITLE
		$curr_y += 14;
		$laporan->SetXY(93, $curr_y); // SETTING THE INVOICE TO THE CENTER OF THE PAGE
		$laporan->SetFont('','B',16);
		$laporan->Write($h_wr,$invoice_header);

		$laporan->SetFont('','',$item_font_size);
		$curr_y += 5;
		//START THE WRITING OF ITEM PAID
		
		// 1. Administration
		if ( $admin_fee > 0 ) {
			$curr_y += 5;
			$laporan->SetXY($curr_x, $curr_y);
			$laporan->Write($h_wr,"Administration");
			$laporan->SetXY($curr_x + $rp_x, $curr_y);
			$laporan->Write($h_wr,"Rp.");
			$laporan->Cell(35,10,$this->IDformat($admin_fee),0,0,'R');
		}
		
		// 2. Consultation
		if ( $doctor_fee > 0 ) {
			$curr_y += 5;
			$laporan->SetXY($curr_x, $curr_y);
			$laporan->Write($h_wr,"Consultation");
			$laporan->SetXY($curr_x + $rp_x, $curr_y);
			$laporan->Write($h_wr,"Rp.");
			$laporan->Cell(35,10,$this->IDformat($doctor_fee),0,0,'R');
		}
		
		
		// 3. PACKAGE
		if ( $package_fee > 0 ) {
			$curr_y += 5;
			$laporan->SetXY($curr_x, $curr_y);
			$laporan->Write($h_wr,"Package");
			$laporan->SetXY($curr_x + $rp_x, $curr_y);
			$laporan->Write($h_wr,"Rp.");
			$laporan->Cell(35,10,$this->IDformat($package_fee),0,0,'R');
		}
		
		// 4. Laboratorium
		if ( $lab_fee > 0 ) {
			$curr_y += 5;
			$laporan->SetXY($curr_x, $curr_y);
			$laporan->Write($h_wr,"Laboratory");
			$laporan->SetXY($curr_x + $rp_x, $curr_y);
			$laporan->Write($h_wr,"Rp.");
			$laporan->Cell(35,10,$this->IDformat($lab_fee),0,0,'R');
			
			if ($type == "R" && $details=="1") {
				$this->load->model('medical','',TRUE);
				$lab_details = $this->medical->get_all_lab_result($app_id);
				$laporan->SetFont('','',9);
				foreach ( $lab_details as $row ) {
					$curr_y += 5;
					$laporan->SetXY($curr_x + 5, $curr_y);
					$laporan->Write($h_wr,"$row->type ($row->specimen)");
				}
				$curr_y += 2;
				$laporan->SetFont('','',$item_font_size);
			}
		}
		
		
		// 5. Tindakan
		if ( $proc_fee > 0 ) {
			$curr_y += 5;
			$laporan->SetXY($curr_x, $curr_y);
			$laporan->Write($h_wr,"Medical Procedure");
			$laporan->SetXY($curr_x + $rp_x, $curr_y);
			$laporan->Write($h_wr,"Rp.");
			$laporan->Cell(35,10,$this->IDformat($proc_fee),0,0,'R');
		}
		
		// 6. Obat
		if ( $medicine_fee > 0 ) {
			$curr_y += 5;
			$laporan->SetXY($curr_x, $curr_y);
			$laporan->Write($h_wr,"Medicine");
			$laporan->SetXY($curr_x + $rp_x, $curr_y);
			$laporan->Write($h_wr,"Rp.");
			$laporan->Cell(35,10,$this->IDformat($medicine_fee),0,0,'R');
			
			// additional for details medicine list
			if ($type == "R" && $details=="1") {
				$this->load->model('appointment','',TRUE);
				$med_list = $this->appointment->get_med_list_from_tb_stock($app_id);
				$laporan->SetFont('','',9);
				foreach ( $med_list as $row ) {
					$curr_y += 5;
					$laporan->SetXY($curr_x + 5, $curr_y);
					$laporan->Write($h_wr,"$row->nama_obat ($row->jumlah $row->unit x Rp. ".$this->IDformat($row->price).")");
				}
			}
			
			// end of additional details medicine list
		}
		
		
		
		
		// DRAWING A LINE BELOW THE PAID ITEM
		$curr_y += 8;
		$laporan->Line($curr_x + $rp_x, $curr_y + 1, $curr_x + $rp_x + 41, $curr_y + 1); // drawing the line above the total; 41 is the length of the line = 41 mm
		
		//CHECKING FOR DISCOUNT
		if ($type == 'R') {
			$disc = $pay_details->disc_percentage;
			if ( $disc > 0 ) {
				$disc_amount = $pay_details->disc_amount;
				
				// writing the sub total
				$laporan->SetFont('','I', $item_font_size - 1);
				$laporan->SetXY($curr_x + $rp_x - 23, $curr_y); // for the sub total; substract with 25 from "Rp."
				$laporan->Write($h_wr,"Sub-Total");
				$laporan->SetFont('','',$item_font_size);
				$laporan->SetXY($curr_x + $rp_x, $curr_y);
				$laporan->Write($h_wr,"Rp.");
				$laporan->Cell(35,10,$this->IDformat($total_payment),0,0,'R');
				
				// writing the discount
				$curr_y += 6;
				$laporan->SetFont('','I',$item_font_size-1);
				$laporan->SetXY($curr_x + $rp_x - 25, $curr_y); // for the additional charge; substract with 30 from "Rp."
				$laporan->Write($h_wr,"Disc ($disc%)");
				$laporan->SetXY($curr_x + $rp_x, $curr_y);
				$laporan->SetFont('','',$item_font_size);
				$laporan->Write($h_wr,"Rp.");
				$laporan->Cell(35,10,$this->IDformat($disc_amount),0,0,'R');
				$total_payment -= $disc_amount;

				// DRAWING A LINE BELOW THE DISCOUNT
				$curr_y += 10;
				$laporan->Line($curr_x + $rp_x, $curr_y + 1, $curr_x + $rp_x + 41, $curr_y + 1); // drawing the line above the total; 41 is the length of the line = 41 mm
				
				$curr_y += 5;
			}
		}
		
		// IF THE CUSTOMER PAYS WITH CREDIT CARD; CHARGE 3%
		// PAYMENT METHOD CREDIT CARD = 2
		/*if ($type == 'R') {
			$payment_method = $pay_details->payment_method;
			if ( $payment_method == '2' ) {
				$add_charge = $total_payment * 0.03;
				
				// writing the sub total
				$laporan->SetFont('','I', $item_font_size - 1);
				$laporan->SetXY($curr_x + $rp_x - 23, $curr_y); // for the sub total; substract with 25 from "Rp."
				$laporan->Write($h_wr,"Sub-Total");
				$laporan->SetFont('','',$item_font_size);
				$laporan->SetXY($curr_x + $rp_x, $curr_y);
				$laporan->Write($h_wr,"Rp.");
				$laporan->Cell(35,10,$this->IDformat($total_payment),0,0,'R');
				
				// writing the additional charge
				$curr_y += 6;
				$laporan->SetFont('','I',$item_font_size-1);
				$laporan->SetXY($curr_x + $rp_x - 30, $curr_y); // for the additional charge; substract with 30 from "Rp."
				$laporan->Write($h_wr,"Surcharge 3%");
				$laporan->SetXY($curr_x + $rp_x, $curr_y);
				$laporan->SetFont('','',$item_font_size);
				$laporan->Write($h_wr,"Rp.");
				$laporan->Cell(35,10,$this->IDformat($add_charge),0,0,'R');
				$total_payment += $add_charge;
				$curr_y += 8;
			}
		}*/
		
		
		// WRITING THE GRAND TOTAL
		
		$laporan->SetFont('','B',13);
		$laporan->SetXY($curr_x + $rp_x - 20, $curr_y); // for the TOTAL; substract with 20 from "Rp."
		$laporan->Write($h_wr,"TOTAL");
		$laporan->SetXY($curr_x + $rp_x, $curr_y);
		$laporan->Write($h_wr,"Rp.");
		$laporan->Cell(34,10,$this->IDformat($total_payment),0,0,'R');
		
		
		// writing the footer; BANK ACCOUNT OF Angsamerah
		
		$curr_y += 10;
		
		$laporan->SetXY($curr_x, $curr_y);
		if ($type == "R") {
			$laporan->SetFont('','BU',$footer_font_size);
			$laporan->Write($h_wr,"Note:");
			$laporan->SetFont('','I');
			$curr_y += 8;
			$laporan->SetXY($curr_x, $curr_y);
			$laporan->MultiCell(170,5,"Valid only if the receipt has the PAID stamp",0,'L');
		} else {
			$laporan->SetFont('','BU',$footer_font_size);
			$laporan->Write($h_wr,"Bank Account:");
			$laporan->SetFont('','I');
			$curr_y += 8;
			$laporan->SetXY($curr_x, $curr_y);
			$laporan->MultiCell(170,5,"PT. Angsamerah, Bank Negara Indonesia (BNI), branch Dukuh Bawah, Jakarta Pusat, Telp No +6221 31923696\nAccount number 20-258-105-3",0,'L');
			$laporan->MultiCell(170,5,"== This is NOT a valid receipt ==",0,'C');
			
		}
		
		//$laporan->MultiCell(180,10,"Angsamerah Clinic\n Graha Media Building 2nd floor\nJl. Blora No. 8-10 Menteng - Jakarta Pusat\nPhone: +6221 3915189",0,'L');
		$laporan->Output();
	}
	
	private function IDformat($x) {
		if ( $x != 0 )
			return number_format($x,0,',','.');
		else
			return "-";
	}
	
	private function long_format_date($date) {
		list($year,$month,$day) = explode("-",$date);
		return date("F j, Y",mktime(0,0,0,$month,$day,$year));
	}
	
	private function month_name($x) {
		switch($x) {
			case 1: return "January";
			case 2: return "February";
			case 3: return "March";
			case 4: return "April";
			case 5: return "May";
			case 6: return "June";
			case 7: return "July";
			case 8: return "August";
			case 9: return "September";
			case 10: return "October";
			case 11: return "November";
			case 12: return "December";
			default: return "NaM";
			
		}
	}
	
	private function create_file_lagu_sion() {
		$this->load->model('appointment','',TRUE);
		$header = "<html><head></head><body>";
		$footer = "</body></html>";
		for ($i = 1; $i <= 342; $i++) {
			$fileName = "lagu_sion_$i.html";
			$lagu = $this->appointment->fetch_lagu($i);
			$handle = fopen($fileName, 'w');			
			$body = "<h3>$lagu->id <span style='font-style:italic'>$lagu->judul_lagu</span></h3><p>".nl2br($lagu->isi_lagu)."</p>";
			fwrite($handle, $header);
			fwrite($handle, $body);			
			fwrite($handle, $footer);			
			fclose($handle);			
		}
		
		echo "Sukses";
	}
	
	
}
