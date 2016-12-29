<?php

class Welcome extends Controller {

	function Welcome()
	{
		parent::Controller();	
		$this->load->library('session');
		$this->load->helper('url');
		if ($this->session->userdata('logged_in') == TRUE)
			redirect ('/general');
	}
	
	function login() 
	{
		/*
		*  IF USER WANTS TO ACCESS DIRECTLY INTO THE LOGIN PART
		*  REDIRECT TO THE INITIAL STATE OF THE LOGIN PAGE (function index)
		*/
		
				
				$user_id = $this->input->post('txt_user_id');
				$pass = $this->input->post('txt_pass');
				$this->load->model('user_amcis','',TRUE);				
				$success = $this->user_amcis->check_user_pass($user_id, $pass);
				if ($success) {
					$newdata = array(									
									'id' => $success['id'],
									'name' => $success['name'],
									'user_id' => $success['user_id'],
									'logged_in' => TRUE,
									'station_desc' => $success['station_desc'],
									'station' => $success['station'],
									'id_doctor' => $success['id_doctor']
									
								);
					$this->user_amcis->change_online_stat($success['id'], 'Y');
					$this->session->set_userdata($newdata);
					redirect('/general');
				} else {
					$this->err_msg("User ID dan/atau Password Anda salah");
				}		
	}
	
	private function err_msg($msg='') {
		$data['baseURL'] = $this->config->item('base_url');
		if ($msg != '') $data['msg']= $msg;
		$data['title'] = "Welcome to AMCIS - Angsamerah Company Information System";
		$this->load->view('login',$data);
	}
	
	function index($msg='')
	{
		$data['baseURL'] = base_url();
		$data['title'] = "Welcome to AMCIS - Angsamerah Company Information System";
		if ($msg == "logout") $data['msg'] = "Successfully Logged Out";
		$this->load->view('login',$data);
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */