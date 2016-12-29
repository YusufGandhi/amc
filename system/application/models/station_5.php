<?php

class Station_5 extends Model {
	
	function Station_5() {
		parent::Model();
	}
	
	function get_tindakan() {
		$sql = "SELECT *
				FROM tb_tindakan";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			print_r($query->result());exit;
			//return $query->result();
		} else
			return FALSE;
	}
	
	/*
	function check_user_pass($userID, $pass)
	{
		$sql = "SELECT tb_user.user_id, tb_user.station_id as station, tb_user.name as name, tb_station.station_desc
				FROM tb_user, tb_station
				WHERE user_id = ? AND pass = ? AND tb_user.station_id = tb_station.id";
		$query = $this->db->query($sql, array($userID,$pass));
		if ($query->num_rows() > 0)
			return $query->row_array();
		else return false;
	}
	
	function change_pass($id, $new_pass) {
		$sql = "UPDATE tb_user SET pass = ? WHERE user_id = ?";
		$query = $this->db->query($sql, array($new_pass, $id));		
		if ($this->db->affected_rows() > 0)
			return true;
		return false;
	}
	
	function check_pass($id, $pass) {
		$sql = "SELECT user_id
				FROM tb_user
				WHERE user_id = ? AND pass =?";
		$query = $this->db->query($sql, array($id, $pass));
		if ($query->num_rows() > 0)			
			return true;
		else return false;
	}*/
}