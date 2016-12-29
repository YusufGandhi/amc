<?php

class Chat_model extends Model {

	function Chat_model()
	{
		parent::Model();		
	}
	
	
	function save($post)
	{
		$data = array(
						'sender_id' => $post['sender_id'],
						'message' => $post['message'],
						'receiver_id' => $post['receiver_id'],
						'timestamp' => date("Y-m-d H:i:s")
					 );
		$this->db->insert('tb_chat_message', $data);
		if ($this->db->affected_rows() > 0)
			return $this->db->insert_id();
		else
			return FALSE;
	}
	
	function check_new_message($group_id, $last_id) {
		$this->db->select_max('id');
		$this->db->from('tb_chat_message');
		$this->db->where('group_id', $group_id);
		
		$query = $this->db->get();
		//return $query->row(1)->id;
		
		if (intval($query->row(1)->id) > intval($last_id)) {
			//return TRUE;
			$sql = "SELECT message, sender_id, MAX(id) as id
					FROM tb_chat_message
					WHERE group_id = ?
					AND id > ?
					ORDER BY id";
			$query1 = $this->db->query($sql, array($group_id, $last_id));			
			return $query1->result();
		} else
			return FALSE;
		
	}
	
	function check_incoming_message($post) {
		$sql = "SELECT tb_chat_message.id, tb_chat_message.sender_id, tb_chat_message.message, tb_user.name
				FROM tb_chat_message, tb_user
				WHERE tb_chat_message.id > ?
				AND tb_chat_message.receiver_id = ?
				AND tb_chat_message.sender_id = tb_user.id
				ORDER BY tb_chat_message.id";
		$query = $this->db->query($sql, array(intval($post['last_message_id']), $post['id']));
		if ($query->num_rows() > 0) {
			return $query->row(1);
		}
		return FALSE;
			
	}
	
	function get_last_id() {
		$this->db->select_max('id');
		$this->db->from('tb_chat_message');
		$query = $this->db->get();
		return $query->row(1)->id;
	}
	
}

// end of file
// Location: controllers/chat.php
