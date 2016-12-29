<?php

class General extends Controller {

	function General()
	{
		parent::Controller();
		$this->load->helper('url');
		$this->load->helper('form');
		$this->load->library('session');		
		if ($this->session->userdata('logged_in') != TRUE) {
				redirect('/welcome');
		}
		//$this->load->model('appointment','',TRUE);
		
	}
	
	
	function index()
	{
		
		$data['baseURL'] = base_url();
		
		switch ($this->session->userdata('station')) {
			case 1: redirect('/control_panel');break;
			case 2: redirect('/station_2');break;					
			case 3: redirect('/station_3');break;
			case 4: redirect('/station_4');break;
			case 5: redirect('/station_5');break;
			default: echo "ERROR USER. PLEASE CONTACT ADMINISTRATOR";
		}		
	}
	
	function source_autocomplete() 
	{
		if (isset($_POST['term'])) {	
			$this->load->model('appointment','',TRUE);
			$result = $this->appointment->search_patient_data($_POST['term']);
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
			redirect('/');
	}
	
	function change_pass() {
		$data['baseURL'] = base_url();
		$data['menu_change_pass'] = TRUE;
		if (isset($_POST['tx_old'])) {
			$old_pass = trim($this->input->post('tx_old'));
			$new_pass = trim($this->input->post('tx_new'));
			$new_confirm = trim($this->input->post('tx_new_confirm'));
			if ($old_pass != "" && $new_pass != "" && $new_confirm != "") {
				$id_admin = $this->input->post('id_admin');
				$this->load->model('user_amcis','',TRUE);
				$valid = $this->user_amcis->check_pass($id_admin, $old_pass);
				if (!$valid) {
					$data['msg'] = "Your old password is incorrect";
				} else if(!($this->input->post('tx_new') === $this->input->post('tx_new_confirm'))) {
					$data['msg'] = "New password didn't match";
				} else 
					$data['change_pass'] = $this->user_amcis->change_pass($id_admin, $this->input->post('tx_new'));		
			} else {
				$data['msg'] = "Please complete the form";
			}
		} 
		$this->load->view('change_pass',$data);		
	}
	
	private function setHeader($excel_file_name)//this function used to set the header variable
	{
		
		/*header("Content-type: application/octet-stream");//A MIME attachment with the content type "application/octet-stream" is a binary file.
		//Typically, it will be an application or a document that must be opened in an application, such as a spreadsheet or word processor. 
		header("Content-Disposition: attachment; filename=$excel_file_name");//with this extension of file name you tell what kind of file it is.
		header("Pragma: no-cache");//Prevent Caching
		header("Expires: 0");//Expires and 0 mean that the browser will not cache the page on your hard drive
		*/
		
		header('Cache-Control: no-store, no-cache, must-revalidate');     // HTTP/1.1
		header('Cache-Control: pre-check=0, post-check=0, max-age=0');    // HTTP/1.1
		header ("Pragma: no-cache");
		header("Expires: 0");
		header('Content-Transfer-Encoding: none');
		header('Content-Type: application/vnd.ms-excel;');                 // This should work for IE & Opera
		header("Content-type: application/x-msexcel");                     // This should work for the rest
		header("Content-Disposition: attachment; filename=$excel_file_name");
		
		/*header('Content-type: application/ms-excel');
		header('Content-Disposition: attachment; filename='.$excel_file_name);*/
	
	
	}
        
    function export_report() {
		if ( isset($_POST['body_report']) ) {
			$body = $_POST['body_report'];
			$title = $_POST['title_report'];
			$this->setHeader($title.".xls");
			echo $body;
		} else
			redirect('/');
	}

	function logout() {
		$this->session->sess_destroy();
		$this->load->model('user_amcis','',TRUE);
		$this->user_amcis->change_online_stat($this->session->userdata('id'),'N');
		redirect('/welcome/index/logout');
	}
}
