<?php

class Patient extends Model {
	
	function Patient() {
		parent::Model();
	}
	
	function insert($post) {
		$curr_year = date('Y');
		$max = $this->count_existing_patient_year($curr_year);
		do {
			$mr_no = $curr_year.".01.".$this->trailing_zero($max);
			$max++;
		} while ($this->check_existing_mr($mr_no));
		if (isset($post['curr_date']))
			$curr_date = $post['curr_date'];
		else
			$curr_date = date("Y-m-d");
		$data = array (
					'mr_no' => $mr_no,
					'nickname' => $post['tx_nickname'],
					'phone_no' => $post['tx_phone'],
					'sex' => $post['rb_sex'],
					'join_date' => $curr_date					
				);
		$this->db->insert('tb_patient',$data);
		if ( $this->db->affected_rows() > 0 ) {
			$MR[0] = $mr_no;
			if (isset($post['with_couple']) ) {
				//echo "no couple";
				$mr_no = $curr_year.".01.".$this->trailing_zero($max);
				$data1 = array (
					'mr_no' => $mr_no,
					'nickname' => $post['tx_couple_nickname'],
					'phone_no' => $post['tx_couple_phone'],
					'sex' => $post['rb_couple_sex'],
					'join_date' => $post['curr_date']					
				);
				$this->db->insert('tb_patient',$data1);
				$MR[1] = $mr_no;
			} 
			return $MR;
			
		} else
			return FALSE;
	}
	
	function get_patient_details($app_id) {
		$sql = "SELECT x.nickname, x.first_name, x.middle_name, x.last_name, YEAR(x.dob) as yob, x.sex, x.mr_no, y.appointment_date, CONCAT(x.first_name, ' ', x.middle_name, ' ', x.last_name) as full_name, x.salutation
				FROM tb_patient as x, tb_appointment as y
				WHERE x.mr_no = y.mr_no
				AND y.appointment_number = ?";
		$query = $this->db->query($sql, $app_id);
		if ($query->num_rows() > 0)
			return $query->row(1);
		else
			return FALSE;
	}
	
	function get_patient_details_by_mr($mr_no) {
		$sql = "SELECT x.nickname, x.first_name, x.middle_name, x.last_name, YEAR(x.dob) as yob, x.sex, x.mr_no, CONCAT(x.first_name, ' ', x.middle_name, ' ', x.last_name) as full_name, x.salutation
				FROM tb_patient as x
				WHERE x.mr_no = ?";
		$query = $this->db->query($sql, $mr_no);
		if ($query->num_rows() > 0)
			return $query->row(1);
		else
			return FALSE;
	}

	function count_visit($mr_no) {
		$sql = "SELECT COUNT(mr_no) as visit
				FROM tb_appointment
				WHERE mr_no = ?
				AND appointment_status = 'A'
				AND examined_status = 'Y'";
		$query = $this->db->query($sql,$mr_no);
		if ($query->num_rows() > 0)
			return ($query->row()->visit + 1);
		else
			return FALSE;
	}
	
		
	// THIS FUNCTION WILL FETCH THE HISTORY OF PARTICULAR
	// PATIENT BY MR # EXCLUDING THE CURRENT APPOINTMENT ($mr_no)
	function visit_history($mr_no,$app_id) {
		$sql = "SELECT appointment_number as app_id
				FROM tb_appointment
				WHERE mr_no = ?
				AND appointment_number <> ?
				AND appointment_status = 'A'
				AND examined_status = 'Y'
				ORDER BY appointment_date ASC";
		$query = $this->db->query($sql,array($mr_no,$app_id));
		if ($query->num_rows() > 0)
			return $query->result();
		else
			return FALSE;
	}
	
	function update_patient_details($post) {
		//$post['tx_mr_no'];
		if ($post['dd_dob']!='' && $post['dd_mob'] != '' &&	$post['dd_yob'] != '')
			$dob = $post['dd_yob']."-".$post['dd_mob']."-".$post['dd_dob'];
		else
			$dob = "0000-00-00";
		$data = array(
			'nickname' => $post['tx_nickname'],
			'salutation' => $post['dd_salutation'],
			'first_name' => $post['tx_firstname'],
			'middle_name' => $post['tx_middlename'],
			'last_name' =>$post['tx_lastname'],
			'sex' => $post['rb_sex'],
			'id_type' => $post['dd_id_type'],
			'id_no' =>$post['tx_id_no'],
			'alamat_id' => $post['tx_street_id'],
			'rt_rw_id' => $post['tx_rt_id']."/".$post['tx_rw_id'],
			'kelurahan_id' => $post['tx_kelurahan_id'],
			'kecamatan_id' =>$post['tx_kecamatan_id'],
			'kota_id' => $post['tx_kota_id'],
			'kdpos_id' => $post['tx_kdpos_id'],
			'alamat_curr' => $post['tx_street_curr'],
			'rt_rw_curr' => $post['tx_rt_curr']."/".$post['tx_rw_curr'],
			'kelurahan_curr' => $post['tx_kelurahan_curr'],
			'kecamatan_curr' => $post['tx_kecamatan_curr'],
			'kota_curr' => $post['tx_kota_curr'],
			'kdpos_curr' =>$post['tx_kdpos_curr'],
			'pob' => $post['tx_pob'],
			'dob' => $dob,
			'phone_no' => $post['tx_primary_hp'],
			'secondary_hp' => $post['tx_secondary_hp'],
			'home_phone' => $post['tx_home_phone'],
			'primary_email' => $post['tx_email_1'],
			'secondary_email' => $post['tx_email_2'],
			'citizenship' => $post['dd_citizenship'],
			'job' => $post['tx_job']
		);
		$where = "mr_no = '".$post['tx_mr_no']."'";
		$str = $this->db->update_string("tb_patient",$data,$where);
		//echo $str;exit;
		$query = $this->db->query($str);
		if ( $this->db->affected_rows() > 0 )
			return TRUE;	
		else
			return FALSE;
		
		
	}
	
	function count_existing_patient_year($year) {
		$sql = "SELECT COUNT(mr_no) as jumlah
				FROM tb_patient
				WHERE mr_no LIKE '".$year."%'";
		$query = $this->db->query($sql);
		if ( $query->num_rows() > 0 )
			return $query->row(1)->jumlah;
		else
			return false;
		
	}
	
	function check_existing_mr($mr_no) {
		$sql = "SELECT mr_no
				FROM tb_patient
				WHERE mr_no = ?";
		$query = $this->db->query($sql, $mr_no);
		if ($query->num_rows() > 0)
			return TRUE;
		else
			return FALSE;
	}
	
	/*
	 *	Trailing zero is a function to make a trailing zero in front of
	 *	the current integer in certain length; e.g: if the integer = 1; so the form will be 00001
	 *	the length default = 5
	 */
	private function trailing_zero($count, $length=5) {
		// first, check whether the variable $count
		// is numeric or not
		if (is_numeric($count)) {
			$count++;
			$num_string = "";
			$length -= strlen($count);
			for ($i = 0; $i < $length; ++$i) {				
				$num_string .= "0";
			}
			$num_string .= $count;
			return $num_string;
		}
		return false;
	}
}