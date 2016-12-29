<?php

class Chat extends Controller {

	function Chat()
	{
		parent::Controller();
		$this->load->helper('url');
		$this->load->helper('form');
		$this->load->library('session');		
		if ($this->session->userdata('logged_in') != TRUE) {
				redirect('/welcome');
		}
		$this->load->model('chat_model','',TRUE);
		//$this->load->model('appointment','',TRUE);
		
	}
	
	
	function save()
	{
		if (isset($_POST['message'])) {
			$chat_id = $this->chat_model->save($_POST);
			if($chat_id)
				echo "$chat_id";
			else
				echo "Failed";
		}
	}
	
	function check_new_message() {
		if(isset($_POST['group_id'])) {
			$group_id = $_POST['group_id'];
			$last_message_id = $_POST['last_message_id'];
			$result = $this->chat_model->check_new_message($group_id, $last_message_id);
			//echo $result;
			$data['response'] = "NO";
			if($result) {
				$data['response'] = "YES";
				foreach($result as $row) {
					$data['message'][] = array(
											'sender_id' => $row->sender_id,
											'content' => $row->message
											);
					$data['last_id'] = $row->id;
				}				
			}
			//$data['id'] = $result;
			echo json_encode($data);
		}
		
	}
	
	function check_message() {
		if(isset($_POST['id'])) {		
			$result = $this->chat_model->check_incoming_message($_POST);
			$data['response'] = 'NO';
			if($result) {
				$data['response'] = 'YES';
				$data['message'] = array(
										'id' => $result->id,
										'sender_id' => $result->sender_id,
										'content' => $result->message,
										'sender_name' => $result->name
										
									);
			}
			echo json_encode($data);
		}
	}
	
	function check_online_users() {
		if(isset($_POST['id'])) {
			$id = $_POST['id'];
			$this->load->model('user_amcis','', TRUE);
			$result = $this->user_amcis->get_online_users($id);
			$data['response'] = "NO";
			if ($result) {
				$data['response'] = "YES";
				foreach($result as $value) {
					$data['message'][] = array(
											'id' => $value->id,
											'name' => $value->name
										);
				}
			}
			
			echo json_encode($data);
		}
		
	}
	
	function get_last_message_id() {
		echo $this->chat_model->get_last_id();
	}
	
	function test($group_id) {
		$result = $this->chat_model->check_new_message($group_id, 0);
		echo $result;
	}
	
}

// end of file
// Location: controllers/chat.php
