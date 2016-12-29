<?php

class Station_3 extends Controller {

	function Station_3()
	{
		parent::Controller();
		$this->load->helper('url');
		$this->load->helper('form');
		$this->load->library('session');		
		if ($this->session->userdata('logged_in') != TRUE || $this->session->userdata('station') != '3') {
				redirect('/welcome');
		}
		//$this->load->model('appointment','',TRUE);
		
	}
	
	
	function index()
	{
		
		$data['baseURL'] = base_url();
		$data['front_logo'] = "lab.jpg";
		$data['title'] = "Control Panel Station ".$this->session->userdata('station')." - ".$this->session->userdata('name');
		$data['custom_message'] = "<h2>Welcome to Control Panel Station ".$this->session->userdata('station')."</h2><br />Please select the operation you need in the menu to your left.";		
		$this->load->view('custom_message',$data);
	}
	
	function list_request($stat='') {
		if ($stat == 'P' or $stat == 'N') {
			$this->load->model('medical','',TRUE);
			$data['baseURL'] = base_url();
			$data['stat'] = $stat;
			$data['result'] = $this->medical->get_new_lab_check_patient($stat);
			if($stat=='N')
				$data['menu_3_new'] = TRUE;
			else if($stat=='P')
				$data['menu_3_pending'] = TRUE;
			$this->load->view('station_3_check_request',$data);
		} else
			redirect('/station_3');
	}
	
	function check_patient($app_id,$stat) {
		$this->load->model('patient','',TRUE);
		$this->load->model('medical','',TRUE);
		if($stat=='N')
			$data['menu_3_new'] = TRUE;
		else if($stat=='P')
			$data['menu_3_pending'] = TRUE;
		$data['insert'] = TRUE;
		$data['patient'] = $this->patient->get_patient_details($app_id);
		$data['patient']->appointment_date = $this->long_format_date($data['patient']->appointment_date);
		$data['lab'] = $this->medical->detil_lab($app_id,$stat);
		//$data['specimen'] = $this->medical->get_specimen();
		$data['curr_year'] = date('Y');
		$data['lab_paramita'] = $this->medical->detil_lab_paramita($app_id,$stat);
		$data['baseURL'] = base_url();
		$data['app_id'] = $app_id;
		$this->load->view('station_3_lab_entry_new',$data);
	}

	/*
	  * function to fetch list item lab if it's fixed
	  * from tb_lab_result_list
	  * if it's not fixed, the result is only an <input type="text" />
	  * @param
	  * @
	  */
	function fetch_result_item_lab() {//$lab_check_id) {
		$lab_check_id = $_POST['id_item'];
		$this->load->model('medical','',TRUE);
		$type = $this->medical->result_lab_type($lab_check_id);
		//print_r($type);exit;
		
		/* result_type : { FREE, PN, FIXED }
		  * FREE: INPUT TEXT
		  * PN: POSITIF & NEGATIF
		  * FIXED: FETCH FROM tb_lab_result_list
		  *
		  * [UPDATE] 30/12/2011
		  * FREE TEXTAREA: TEXTAREA
		  */ 
		if ( $type == "FREE" ) {
			echo "<input type=\"text\" id=\"item_lab_value\" />";
		} else if ( $type == "FREE TEXTAREA" ) {
			echo "<br /><textarea wrap=\"hard\" id=\"item_lab_value\" cols=\"5\"></textarea>";
		} else if ( $type == "PN" ) {
			echo "<select id=\"item_lab_value\">";
			echo "	<option value=\"POSITIF\">POSITIF</option>";
			echo "	<option value=\"NEGATIF\">NEGATIF</option>";
			echo "</select>";
		} else if ( $type == "FIXED" ){
			$list = $this->medical->list_item_lab($lab_check_id);
			//print_r($list);exit;
			if (!empty($list)) {
				echo "<select id=\"item_lab_value\">";
				foreach ($list as $row) {
					echo "	<option value=\"$row->item\">$row->item</option>";
				}
				echo "</select>";
			} else
				echo "$type";
			
		}		
	}
	
	function save_result() {
		//print_r($_POST);exit;
		if (isset($_POST['app_id'])) {
			$this->load->model('medical','',TRUE);
			$save = $this->medical->save_lab_check($_POST);
			
				//redirect("/station_3/print_result/".$_POST['app_id']);
			if ($save) { 
				if ($save == "D") { 
					$data['link'] = site_url('station_3/print_result/'.$_POST['app_id']);
					$hiv = $this->medical->has_HIV($_POST['app_id']);
					if($hiv) {
						$data['hiv_link'] = site_url('station_3/print_result/'.$_POST['app_id'].'/1');
					} 
					$data["print_lab"] = TRUE;
				}
				$data['custom_message'] = "Save result successful";
			} else {
				$data['custom_message'] = "Save failed!";
			}
			$data['baseURL'] = base_url();
			$data['menu_3_new'] = TRUE;
			$this->load->view('custom_message',$data);
		} else {
			redirect('/station_3');
		}
	}
	
	function test_post() {
		print_r($_POST);exit;
	}
	
	private function long_format_date($date) {
		list($year,$month,$day) = explode("-",$date);
		return date("F j, Y",mktime(0,0,0,$month,$day,$year));
	}
	
	function source_autocomplete() 
	{
		if (isset($_POST['term'])) 
		{
			$this->load->model('medical','',TRUE);
			$result = $this->medical->search_patient_data_lab($_POST['term']);
			$data['response'] = 'false';
			if (!empty($result)) {
				$data['response'] = 'true';
				$data['message'] = array();
				foreach($result as $row) {
					$data['message'][] = array( 'id' => $row->mr_no,
												'label' => $row->nickname
											);
				}			
			}
			echo json_encode($data);
		} else
			redirect('/station_3');
	}
	
	function result() {
		if (isset($_POST['tx_mr_no'])) {
			$data['search'] = TRUE;
			$this->load->model('appointment','',TRUE);
			$this->load->model('medical','',TRUE);
			$data['data_visit'] = $this->appointment->get_appointment_by_mr_lab($_POST['tx_mr_no']);
			$data['mr_no'] = $_POST['tx_mr_no'];
			$data['baseURL'] = base_url();
			$data['menu_3_history']= TRUE;
			//print_r($data);exit;
			$this->load->view('station_3_search_by_mr',$data);
		} else
			redirect('/station_3');
	}
	
	function history() {
		$data['baseURL'] = base_url();
		$data['search'] = TRUE;
		$data['menu_3_history']= TRUE;
		$this->load->view('station_3_search_by_mr',$data);
	}
	
	function print_result($app_id,$HIV = FALSE) {
		$this->load->model('medical','',TRUE);
		$this->load->model('patient','',TRUE);
		
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
		$rp_x = 120; // the margin for "Rp." from the curr_x
		
		// for the Write() height parameter, so all will have the same value
		$h_wr = 13;
		$baseURL = base_url(); 
		
		$header = "LAB RESULT";
		
		$lab_result = $this->medical->get_lab_result($app_id,$HIV);
		// PAY ATTENTION TO THIS!
		// THIS HAS TO BE THE RIGHT SALUTATION AND FULL NAME
		$detail = $this->patient->get_patient_details($app_id);
		$salutation = $detail->salutation;
		$full_name = (trim($detail->full_name) != "" ? $detail->full_name: $detail->nickname);//"Test";//trim("$price->first_name $price->middle_name $price->last_name");
		$visit_date = $this->long_format_date($detail->appointment_date);
		$date = date("F d, Y");
		
		
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
		$laporan->Write($h_wr,"Visit: $visit_date");
		
		
		
		// WRITING THE RESULT LAB HEADER
		$curr_y += 14;
		$laporan->SetXY(93, $curr_y); // SETTING THE INVOICE TO THE CENTER OF THE PAGE
		$laporan->SetFont('','B',16);
		$laporan->Write($h_wr,$header);

		// WRITING THE HEADER TABLE OF THE RESULT LAB
		$laporan->SetFont('','B',$item_font_size);
		$curr_y += 12;
		
		$laporan->SetXY($curr_x + 5, $curr_y);
		$laporan->Write($h_wr,"No.");
		$laporan->SetXY($curr_x + 20, $curr_y);
		$laporan->Write($h_wr,"ITEM");
		$laporan->SetXY($curr_x + $rp_x, $curr_y);
		$laporan->Write($h_wr,"RESULT");
		
		//START THE WRITING OF RESULT LAB
		// UPDATE: fEB 16, 2011
		// HIV must not be written with other item labs
		// it must be in a separated repot
		$laporan->SetFont('','',$item_font_size);
		$entry_by = "";
		$i = 1;
		foreach( $lab_result as $row ) {
			if ($row->result == "X")
				continue;
			$curr_y += 7;
			$laporan->SetXY($curr_x + 5, $curr_y);
			$laporan->Write($h_wr,$i++.".");
			$laporan->SetXY($curr_x + 13, $curr_y);
			$laporan->Write($h_wr,$row->type);
			$laporan->SetXY($curr_x + $rp_x, $curr_y);
			$laporan->Write($h_wr,$row->result);
			if ($entry_by == "") $entry_by = $row->result_entry_by;
			//$laporan->Cell(35,10,$this->IDformat($admin_fee),0,0,'R');
			
		}
		
		// writing the place & date
		$curr_y += 15;
		$laporan->SetXY($add_x, $curr_y);
		$footer = "Jakarta, $date\n\n\n\n$entry_by";
		$laporan->MultiCell(0,7,$footer,0,'C');
		/*$laporan->Write($h_wr,"Jakarta, ".$date);
		$curr_y += 25;
		$laporan->SetXY($add_x, $curr_y);
		$laporan->Write($h_wr,"Pembuat");*/
		
		$laporan->Output();
	}
	
	
	function report($type="") {
		if (isset($_POST['report_month'])) {
			//print_r($_POST);exit;
			$data['report_month'] = $_POST['report_month'];
			$data['month_name'] = $this->month_name($_POST['report_month']);
			$data['report_year'] = $_POST['report_year'];
			$this->load->model('medical','',TRUE);
			$result = $this->medical->patient_lab_checked_per_month($_POST);//lab_done_per_month($_POST);
			if ($result) {
				
				$data['report_data'] = $result;
				foreach ( $result as $row) {				
					
					// for angsamerah lab check
					$transaction = $this->medical->detil_lab_checked_per_appointment($row->id_appointment);
					$data['transaction'][$row->mr_no]['am'] = $transaction;
					
					// for pramita lab check
					$transaction = $this->medical->detil_lab_pramita_checked_per_appointment($row->id_appointment);
					$data['transaction'][$row->mr_no]['pr'] = $transaction;
				}
				/*
				if ($type==="pdf") {
					$this->pdf_report($data);
					exit;
				}
					*/
			} else
				$data['report_data'] = FALSE;
					
			
		}
		$data['baseURL'] = base_url();
		$data['title'] = "Report Station 3";
		$data['menu_4_report'] = TRUE;
		$data['menu_4_report'] = TRUE;
		//$data['no_nav'] = TRUE;		
		$this->load->view('report_station_3',$data);
	
	}
	
	private function month_name($m) {
		switch($m) {
			case 1: return "Januari";
			case 2: return "Februari";
			case 3: return "Maret";
			case 4: return "April";
			case 5: return "Mei";
			case 6: return "Juni";
			case 7: return "Juli";
			case 8: return "Agustus";
			case 9: return "September";
			case 10: return "Oktober";
			case 11: return "November";
			case 12: return "Desember";
		}
	}
	
	
}
