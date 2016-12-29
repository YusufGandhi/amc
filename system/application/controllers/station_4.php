<?php

class Station_4 extends Controller {

	function Station_4()
	{
		parent::Controller();
		$this->load->helper('url');
		$this->load->helper('form');
		$this->load->library('session');		
		if ($this->session->userdata('logged_in') != TRUE || $this->session->userdata('station') != '4') {
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
	
	function stokist_obat($jenis="Bebas") {
		if (isset($_POST['id_obat'])) {
			$this->load->model('medical','',TRUE);
			$update = $this->medical->update_stock_obat($_POST);
			if ( $update )
				$data['success'] = TRUE;
			else
				$data['failed'] = TRUE;
			
		}
		$data['jenis'] = $jenis;
		$data['menu_4_stokist_obat'] = TRUE;
		$data['baseURL'] = base_url();
		$data['title'] = "Stokist Obat Bebas";
		$data['search'] = TRUE;
		$data['form_validator'] = TRUE;
		$this->load->view('station_4_stokist_obat_bebas',$data);
	}
	
	function obat_autocomplete($jenis="") {
		if (isset($_POST['term'])) {	
			$this->load->model('medical','',TRUE);
			$result = $this->medical->get_obat(TRUE,$_POST['term'],$jenis);
			$data['response'] = 'false';
			if (!empty($result)) {
				$data['response'] = 'true';
				$data['message'] = array();
				foreach($result as $row) {
					$data['message'][] = array( 'id' => $row->id,
												'label' => $row->nama_obat,
												'unit' => $row->unit,
												'stock' => $row->current_stock,
												'price' => $row->price
											);
				}			
			}
			echo json_encode($data);
		} else
			redirect('/');
	}
	
	function pengambilan_obat() {
		if (isset($_POST['id_obat'])) {
			$this->load->model('medical','',TRUE);
			$update = $this->medical->update_stock_obat($_POST);
			if ( $update )
				$data['success'] = TRUE;
			else
				$data['failed'] = TRUE;
		}
		$data['jenis'] = "Bebas";
		$data['baseURL'] = base_url();
		$data['search_keluar'] = TRUE;
		$data['title'] = "Pengambilan Obat";
		$data['menu_4_pengambilan_obat'] = TRUE;
		$this->load->view('station_4_pengambilan_obat',$data);
	}
	
	function pengambilan_obat_terbatas() {
		if (isset($_POST['id_obat'])) {
			$this->load->model('medical','',TRUE);
			$update = $this->medical->update_stock_obat($_POST);
			if ( $update )
				$data['success'] = TRUE;
			else
				$data['failed'] = TRUE;
		}
		$data['jenis'] = "Terbatas";
		$data['baseURL'] = base_url();
		$data['search_keluar'] = TRUE;
		$data['title'] = "Pengambilan Obat";
		$data['menu_4_pengambilan_obat'] = TRUE;
		$this->load->view('station_4_pengambilan_obat',$data);
	}
	
	function add_med_data() {
		if (isset($_POST['nama_obat'])) {
			$this->load->model('medical','',TRUE);
			if ($success = $this->medical->add_data_obat($_POST))
				$data['form_save'] = TRUE;
			else
				$data['form_failed'] = TRUE;
			
		}
		$data['baseURL'] = base_url();
		$data['menu_4_add_obat'] = TRUE;
		$data['add_data_obat'] = TRUE;
		$data['form_validator'] = TRUE;
		$this->load->view('station_4_input_obat',$data);
	}
	
	function view_med_data($form_save="") {
		$this->load->database();
		$this->load->library('pagination');
		$data['baseURL'] = base_url();
		$data['menu_4_add_obat'] = TRUE;
	    $config['base_url'] = base_url().'index.php/station_4/view_med_data/';
	    $config['total_rows'] = $this->db->count_all('tb_obat');
	    $config['per_page'] = '10';
	    $config['full_tag_open'] = '<div">';
	    $config['full_tag_close'] = '</div>';
		if ($form_save === "TRUE")
			$data['form_save'] = TRUE;
		//$config['first_link'] = "<<";

	    $this->pagination->initialize($config);
			
	    //load the model and get results
	    $this->load->model('medical');
	    $data['results'] = $this->medical->get_data_obat($config['per_page'],$this->uri->segment(3));
			
	    // load the HTML Table Class
	    $this->load->library('table');
		$tmpl = array (
	                    'table_open'          => '<table class="report">',
	                    'row_start'           => '<tr class="odd">',
	                    'row_alt_start'       => '<tr class="even">'
	                    
				   	  );
		$this->table->set_template($tmpl);
	    //$this->table->set_heading('ID', 'Title', 'Author', 'Description');
			
	    // load the view
	    //$this->load->view('books_view', $data);
		//print_r($data);exit;
		$this->load->view('station_4_view_obat',$data);
	}
	
	// not used 
	
	function update_stock() {
		//$this->load->model('medical','',TRUE);
		print_r($_POST);
		/*$update = $this->medical->update_stock_obat($_POST);
		if ( $update ) {
			echo "success";
		} else {
			echo "failed";
		}*/		
	}
	
	function edit_obat($id="") {
		$this->load->model('medical','',TRUE);
		if (isset($_POST['nama_obat'])) {
			//print_r($_POST);exit;
			$update = $this->medical->update_data_obat($_POST);
			//$data['form_save'] = TRUE;			
			//redirect($this->view_med_data(TRUE);
			redirect('/station_4/view_med_data/TRUE');
			
		}
		$data['menu_4_add_obat'] = TRUE;
		$details = $this->medical->check_obat_exist($id);
		if ($details) {
			$data['details'] = $details;
			$data['baseURL'] = base_url();
			
			$this->load->view('station_4_edit_obat',$data);
		} else
			redirect('/station_4/view_med_data');
	}
	
	function report($type="") {
		if (isset($_POST['report_month'])) {
			//print_r($_POST);exit;
			$data['report_month'] = $_POST['report_month'];
			$data['month_name'] = $this->month_name($_POST['report_month']);
			$data['report_year'] = $_POST['report_year'];
			$this->load->model('medical','',TRUE);
			$result = $this->medical->report_obat_keluar($_POST);
			if ($result) {
				
				$data['report_data'] = $result;
				foreach ( $result as $row) {
				
					$transaction = $this->medical->detil_obat_keluar_per_item($row->id_obat,$_POST);
					$data['transaction'][$row->id_obat] = $transaction;
				}
				
				if ($type==="pdf") {
					$this->pdf_report($data);
					exit;
				}
					
			} else
				$data['report_data'] = FALSE;
					
			
		}
		$data['baseURL'] = base_url();
		$data['title'] = "Report Station 4";
		//$data['$menu_4_report'] = TRUE;
		$data['menu_4_report'] = TRUE;
		//$data['no_nav'] = TRUE;		
		$this->load->view('report_station_4',$data);
	
	}
	
	private function pdf_report($data) {
		require ('pdf.php');
		$laporan = new PDF();
		$laporan->SetFont('Arial','',11);
		$laporan->AddPage();
		$laporan->AngsamerahLogo();
		$header = array("No.","MR No","Tanggal","Jumlah","Harga Jual","Total","Sisa Stok");
		$laporan->Cell(0,0, strtoupper("Laporan Obat Keluar Bulan ". $this->month_name($data['report_month'])." ".$data['report_year'])."",0,1,'C',false);
		$laporan->Ln(10);;
		foreach($data['report_data'] as $row) {					
			$print_data = $data['transaction'][$row->id_obat];
			$laporan->SetTextColor(255,0,0);
			$laporan->SetFont('','B');
			$laporan->Write(12, "$row->nama_obat");
			$laporan->SetFont('');
			$laporan->SetTextColor(0,0,0);
			//$laporan->Write(10, " (Stok Awal: $row->saldo_awal_bulan)");
			//for($i=0; $i < 10 ; $i++) {
			$laporan->Ln();
			$laporan->Report_Table_Station_4($header,$print_data,$row->saldo_awal_bulan);			
			$laporan->Ln(7);			
		}
		$laporan->Output();
		
	}
	
	function patient_list() {
		$this->load->model('appointment','',TRUE);
		$data['baseURL'] = base_url();
		$data['menu_4_pengambilan_obat'] = TRUE;
		// FETCH THE DATA OF PATIENT NOT YET EXAMINED
		// PARAMETER USING 'N' FOR THE FUNCTION GET PATIENT EXAMINED
		// UPDATED JANUARY 12, 2011: FETCH BASED ON DOCTOR ID
		$data['result'] = $this->appointment->get_patient_med_queue();//print_r($data);exit;
		$this->load->view('station_4_med_queue',$data);
	}
	
	function medicine_list($app_id) {
		$this->load->model('appointment','',TRUE);
		if ($this->appointment->existing_app_no($app_id)) {
		//$this->load->model('medical','',TRUE);
			$data['menu_4_pengambilan_obat'] = TRUE;
			$data['result'] = $this->appointment->get_med_list($app_id);
			$data['app_id'] = $app_id;
			$data['baseURL'] = base_url();			
			$this->load->view('station_4_med_list',$data);
		} else
			redirect('/');
		
	}
	
	function save_med() {
		//print_r($_POST);exit;
		if (isset($_POST['operasi'])) {
			$data['menu_4_pengambilan_obat'] = TRUE;			
			$data['baseURL'] = base_url();
			$this->load->model('medical','',TRUE);			
			$success = $this->medical->update_stock_obat($_POST);
			if ( $success )
				$data['custom_message'] = "<div class='success'>Successful</div>";
			else
				$data['custom_message'] = "Failed";
			$this->load->view('custom_message',$data);
		} else
			redirect('/');
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
	
	

// END OF FILE
// location system/application/controllers/station_4.php