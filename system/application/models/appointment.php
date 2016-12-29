<?php

class Appointment extends Model {
	
	function Appointment() {
		parent::Model();
	}
	
	function get_doctor_list($type='') {
		$sql = "SELECT * FROM tb_doctor WHERE type=? ORDER BY id";
		$query = $this->db->query($sql,$type);
		if ($query->num_rows() > 0)
			return $query->result();
		else
			return FALSE;
	}
	
	function get_room_list() {
		$sql = "SELECT * FROM tb_room";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0)
			return $query->result();
		else
			return FALSE;
	}
	
	function get_nurse_list() {
		$query = $this->db->get('tb_nurse');
		return $query->result();		
	}
	
	function get_nurse_name_by_app($app_id) {
		$sql = "SELECT x.name
				FROM tb_nurse as x, tb_appointment as y
				WHERE x.id = y.nurse_id
				AND y.appointment_number = ?";
		$query = $this->db->query($sql, $app_id);
		if ($query->num_rows() > 0)
			return $query->row(1)->name;
		else
			return FALSE;
	}
	
	function get_doctor_by_app($app_id) {
		$sql = "SELECT x.id, x.name, x.type, y.other_doctor_name
				FROM tb_doctor as x, tb_appointment as y
				WHERE x.id = y.doctor_id
				AND y.appointment_number = ?";
		$query = $this->db->query($sql, $app_id);
		if ($query->num_rows() > 0)
			return $query->row(1);
		else
			return FALSE;
	}
	
	function get_nurse_name($id) {
		$sql = "SELECT name FROM tb_nurse WHERE id = ?";
		$query = $this->db->query($sql, $id);
		if ($query->num_rows() > 0)
			return $query->row(1)->name;
		else
			return FALSE;
	}
	
	function get_doctor_name($id) {
		$sql = "SELECT name FROM tb_doctor WHERE id=?";
		$query = $this->db->query($sql,$id);
		if ($query->num_rows() > 0) {
			 $row = $query->row(1);
			 return $row->name;
		} else
			return FALSE;
	}
	
	function get_hour_list() {
		$sql = "SELECT * FROM tb_hour";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0)
			return $query->result();
		else
			return FALSE;
	}
	
	function get_hour_by_id($id) {
		$sql = "SELECT hour FROM tb_hour WHERE id=? LIMIT 1";
		$query = $this->db->query($sql,$id);
		$row = $query->row(1);
		if ($query->num_rows() > 0)
			return $row->hour;
		else
			return FALSE;
	}
	
	function get_end_hour_by_id($id) {
		$sql = "SELECT end FROM tb_hour WHERE id=? LIMIT 1";
		$query = $this->db->query($sql,$id);
		if ($query->num_rows() > 0) {
			$row = $query->row(1);
			return $row->end;
		} else 
			return FALSE;
	}
	
	function check_existing_appointment($date, $room, $hour) {
		$sql = "SELECT doctor_id, mr_no, id_hour, id_hour_end
				FROM tb_appointment
				WHERE appointment_date = ? AND id_hour = ? AND room_id = ? AND appointment_status = 'P'";
		$query = $this->db->query($sql,array($date,$hour,$room));
		if ($query->num_rows() > 0)
			return $query->row(1);
		else
			return FALSE;				
	}
	
	function get_mr_no_by_appointment_id($app_id) {
		$sql = "SELECT mr_no
				FROM tb_appointment
				WHERE appointment_number = ?";
		$query = $this->db->query($sql, $app_id);
		if($query->num_rows() > 0)
			return $query->row(1)->mr_no;
		else
			return FALSE;
	}
	
	function get_details_appointment($app_id) {
		$sql = "SELECT x.appointment_number, x.mr_no, x.couple_appointment_id, x.appointment_date, x.id_hour, x.id_hour_end, x.doctor_id, x.other_doctor_name, x.nurse_id, x.room_id, x.keluhan, x.temp_diagnosis, y.type
				FROM tb_appointment as x, tb_doctor as y
				WHERE x.doctor_id = y.id
				AND x.id = ?";
		$query = $this->db->query($sql,$app_id);
		if ($query->num_rows() > 0)
			return $query->row(1);
		else
			return FALSE;
		
	}
	
	function check_existing_appointment_doctor($date, $hour, $doctor) {
		$sql = "SELECT id_hour, id_hour_end
				FROM tb_appointment
				WHERE doctor_id = ? AND appointment_date = ? AND id_hour = ? AND appointment_status = 'P'
				LIMIT 1";
		$query = $this->db->query($sql,array($doctor,$date,$hour));
		if ($query->num_rows() > 0)
			return $query->row(1);
		else
			return FALSE;
	}
	
	function check_doctor_hour_date($date, $doctor, $hour) {
		$sql = "SELECT mr_no, id_hour, id_hour_end
				FROM tb_appointment
				WHERE appointment_date = ? AND doctor_id = ? AND id_hour = ? AND appointment_status = 'P'";
		$query = $this->db->query($sql,array($date, $doctor, $hour));
		if ($query->num_rows() > 0)
			return $query->row(1);
		else
			return FALSE;
	}
	
	function update_appointment_status($id, $status) {
		//if ($examined=='Y')
		//echo $id;		
		$sql = "UPDATE tb_appointment SET appointment_status = ?, examined_status = 'N' WHERE id = ? LIMIT 1";
		/*else	
			ATTENTION! ATTENTION! ATTENTION!
			WORKING HERE, CHECK WHETHER THERE'S A COUPLE OR NOT
			BY CHECKING FIELD couple_appointment_id
			
			$sql = "UPDATE tb_appointment SET appointment_status = ? WHERE id = ? LIMIT 1";*/
		$query = $this->db->query($sql, array($status,$id));
		
		if ($this->db->affected_rows() > 0) {
			$couple = $this->check_couple_appointment_id($id);
			if ($couple) {
				$update_couple = $this->update_appointment_status_app_id($couple, $status);
				if ($update_couple)
					return TRUE;
			}
			return TRUE;
		}
		
		return FALSE;
	}
	
	function update_appointment_status_app_id($app_id, $status) {
		$sql = "UPDATE tb_appointment SET appointment_status = ?, examined_status = 'N' WHERE appointment_number = ? LIMIT 1";
		
		$query = $this->db->query($sql, array($status,$app_id));
		
		if ($this->db->affected_rows() > 0)			
			return TRUE;
		else 
			return FALSE;
	}
	
	// WILL RETURN ID COUPLE APPOINTMENT IF COUPLE EXISTS
	// OR WILL RETURN FALSE IF NO COUPLE
	function check_couple_appointment_id($app_id) {
		$sql = "SELECT couple_appointment_id
				FROM tb_appointment
				WHERE id = ?";
		$query = $this->db->query($sql,$app_id);
		if ($query->num_rows() > 0)
			return $query->row(1)->couple_appointment_id;
		else
			return FALSE;
		
	}
	
	function check_couple_appointment_id_by_app($app_id) {
		$sql = "SELECT couple_appointment_id
				FROM tb_appointment
				WHERE appointment_number = ?";
		$query = $this->db->query($sql,$app_id);
		if ($query->num_rows() > 0)
			return $query->row(1)->couple_appointment_id;
		else
			return FALSE;
		
	}
	
	function update_appointment_visit_detail($post) {
		$data = array(
						'anamnesa' => $post['anamnesa'],
						'blood_pressure' => $post['sistole']."/".$post['diastole'],
						'nadi' => $post['nadi'],
						'temperature' => $post['temperature'],
						'breath' => $post['breath'],
						'physical_notes' => $post['notes_physic'],
						'd_banding' => $post['d_banding'],
						'd_kerja' => $post['d_kerja'],
						'other_meds' => $post['other_meds']
						
					 );
		$where = "appointment_number = '".$post['app_id']."'";
		$sql = $this->db->update_string('tb_appointment',$data,$where);
		$query = $this->db->query($sql);
		if ($this->db->affected_rows() > 0)
			return TRUE;
		else
			return FALSE;
	}
	
	function update_appointment_details($post) {
		$data = array(
					
					'appointment_date' => $post['date'],
					'id_hour' => $post['dd_list_hour_start'],
					'id_hour_end' => $post['dd_list_hour_end'],
					'doctor_id' => $post['dd_list_doctor'],
					'nurse_id' => $post['dd_list_nurse'],
					'other_doctor_name' => $post['other_name'],
					'room_id' => $post['dd_list_room'],					
					'keluhan' => $post['tx_keluhan'],
					'temp_diagnosis' => $post['tx_temp_diagnosis'],
					'entered_by' => $this->session->userdata('name'),
					'created_date' => date("Y-m-d H:i:s")					
					
				);
				//'appointment_status' => 'P',				
				//'mr_no' => $post['mr_no'],
				//'patient_type' => $post['patient_type'],
				//'appointment_number' => $post['app_id'],
		$where = "appointment_number = '".$post['app_id']."'";
		$sql = $this->db->update_string('tb_appointment',$data,$where);
		$query = $this->db->query($sql);
		if ($this->db->affected_rows() > 0) {
			if (isset($post['couple_app_no'])) {
				$where = "appointment_number = '".$post['couple_app_no']."'";
				$sql = $this->db->update_string('tb_appointment',$data,$where);
				$query = $this->db->query($sql);
				if ($this->db->affected_rows() > 0)
					return TRUE;
			}
			return TRUE;
			
		} else
			return FALSE;
		
	}
	
	function insert_appointment($post) {
		//list($hour, $room) = explode('-',$post['availability']);
		// BLR.10.00001
		$curr_year = date('y');
		$max = $this->max_appointment($curr_year);
		do {
			$app_no = "BLR.". $curr_year.".".$this->trailing_zero($max);
			$max++;
		} while ($this->existing_app_no($app_no));
		//if (!isset($post['med_only'])) {
			$data = array(
						'mr_no' => $post['mr_no'],
						'patient_type' => $post['patient_type'],
						'appointment_number' => $app_no,
						'appointment_date' => $post['date'],
						'id_hour' => $post['dd_list_hour_start'],
						'id_hour_end' => $post['dd_list_hour_end'],
						'doctor_id' => $post['dd_list_doctor'],
						'nurse_id' => $post['dd_list_nurse'],
						'other_doctor_name' => $post['other_name'],
						'room_id' => $post['dd_list_room'],
						'appointment_status' => 'P',
						'keluhan' => $post['tx_keluhan'],
						'temp_diagnosis' => $post['tx_temp_diagnosis'],
						'entered_by' => $this->session->userdata('name'),
						'created_date' => date("Y-m-d H:i:s")
					);
		/*} else {
			$data = array(
						'mr_no' => $post['mr_no'],
						'patient_type' => $post['patient_type'],
						'appointment_number' => $app_no,						
						'appointment_status' => 'P',						
						'entered_by' => $this->session->userdata('name'),
						'created_date' => date("Y-m-d H:i:s")
					);

		}*/
		$this->db->insert("tb_appointment",$data);
		$app_id[0] = $app_no;
		if ($this->db->affected_rows() > 0) {
			if (isset($post['mr_no1'])) {
				$app_no1 = "BLR.". $curr_year.".".$this->trailing_zero($max);
				$data1 = array(
								'mr_no' => $post['mr_no1'],
								'patient_type' => $post['patient_type'],
								'appointment_number' => $app_no1,
								'appointment_date' => $post['date'],
								'couple_appointment_id' => $app_no,
								'id_hour' => $post['dd_list_hour_start'],
								'id_hour_end' => $post['dd_list_hour_end'],
								'doctor_id' => $post['dd_list_doctor'],
								'nurse_id' => $post['dd_list_nurse'],
								'other_doctor_name' => $post['other_name'],
								'room_id' => $post['dd_list_room'],
								'appointment_status' => 'P',
								'keluhan' => $post['tx_keluhan'],
								'temp_diagnosis' => $post['tx_temp_diagnosis'],
								'entered_by' => $this->session->userdata('name'),
								'created_date' => date("Y-m-d H:i:s")
							);
				$this->db->insert("tb_appointment",$data1);
				$data2 = array( 'couple_appointment_id' => $app_no1 );
				$where = "appointment_number = '$app_no'";
				$sql = $this->db->update_string('tb_appointment',$data2,$where);
				$query = $this->db->query($sql);
				$app_id[1] = $app_no1;
			}
			return $app_id;
		} else
			return FALSE;
	}
	
	function max_appointment($year) {
		$sql = "SELECT COUNT(id) as jumlah
				FROM tb_appointment
				WHERE appointment_number LIKE 'BLR.".$year."%'";
		$query = $this->db->query($sql);
		if ( $query->num_rows() > 0 )
			return $query->row(1)->jumlah;
		else
			return false;
	}
	
	function existing_app_no($id) {
		$sql = "SELECT appointment_number
				FROM tb_appointment
				WHERE appointment_number = ?";
		$query = $this->db->query($sql,$id);
		if ($query->num_rows() > 0)
			return TRUE;
		else
			return FALSE;
	}
	
	function get_med_list($app_id) {
		$sql = "SELECT y.id as id, y.nama_obat as nama_obat, x.amount as jumlah,
				(y.price * 1.33) as price, y.current_stock as current_stock
				FROM tb_obat_details_app as x, tb_obat as y
				WHERE x.id_appointment = ?
				AND x.id_obat = y.id";
		$query = $this->db->query($sql,$app_id);
		if ($query->num_rows() > 0)
			return $query->result();
		else
			return FALSE;
	}
	
	function get_med_list_from_tb_stock($app_id) {
		$sql = "SELECT y.id as id, y.nama_obat as nama_obat,
				x.amount as jumlah,	x.price, y.unit
				FROM tb_log_stock_obat as x, tb_obat as y
				WHERE x.id_appointment = ?
				AND x.id_obat = y.id
				AND operation = 'O'";
		$query = $this->db->query($sql,$app_id);
		if ($query->num_rows() > 0)
			return $query->result();
		else
			return FALSE;
	}
	
	function search_patient_data($keyword) {
		$sql = "SELECT * 
				FROM tb_patient
				WHERE nickname LIKE '%". $keyword ."%' OR mr_no LIKE '%". $keyword ."%'";
		$query = $this->db->query($sql);
		return $query->result();
		//return FALSE;
	}
	
	function search_patient_data_appointment($keyword) {
		$sql = "SELECT tb_patient.mr_no as id, tb_patient.nickname as nickname, tb_appointment.id as app_id, tb_appointment.appointment_date as date,
				tb_appointment.id_hour as hour, tb_appointment.id_hour_end as end_hour, tb_doctor.name as doctor, tb_room.room_number as room,
				tb_appointment.couple_appointment_id as couple_app_id,
				( SELECT x.nickname
				  FROM tb_patient as x, tb_appointment as y
				  WHERE x.mr_no = y.mr_no
				  AND y.appointment_number = couple_app_id
				) as couple_nickname
				FROM tb_patient, tb_appointment, tb_hour, tb_doctor, tb_room
				WHERE (tb_patient.nickname LIKE '%". $keyword ."%' OR tb_patient.mr_no LIKE '%". $keyword ."%')
				AND tb_patient.mr_no = tb_appointment.mr_no
				AND tb_appointment.appointment_status = 'P'
				AND tb_appointment.id_hour = tb_hour.id
				AND tb_appointment.doctor_id = tb_doctor.id
				AND tb_appointment.room_id = tb_room.id";
		$query = $this->db->query($sql);
		return $query->result();
		//return FALSE;
	}
	
	function get_sex_by_mr($key) {
		$sql = "SELECT sex
				FROM tb_patient
				WHERE mr_no = ?
				LIMIT 1";
		$query = $this->db->query($sql,$key);
		$row = $query->row(1);
		return $row->sex;
	}
	
	function get_appointment_by_mr($mr) {
		$sql = "SELECT x.appointment_number as app_id, x.appointment_date as date, y.name as doctor, y.id as doctor_id
				FROM tb_appointment as x, tb_doctor as y
				WHERE mr_no = ?
				AND x.doctor_id = y.id";
		$query = $this->db->query($sql,$mr);
		return $query->result();
	}
	
	function get_appointment_by_mr_lab($mr) {
		$sql = "SELECT x.appointment_number as app_id, x.appointment_date as date, y.name as doctor
				FROM tb_appointment as x, tb_doctor as y
				WHERE mr_no = ?
				AND x.doctor_id = y.id
				AND x.lab_check_status = 'D'";
		$query = $this->db->query($sql,$mr);
		return $query->result();
	}
	
	function daily_report($date) {
		$sql = "SELECT * 
				FROM tb_appointment
				WHERE appointment_date = ?";
		$query = $this->db->query($sql, $date);
		return $query->result();
	}
	
	function monthly_report($month,$year) {
		$sql = "SELECT *
				FROM tb_appointment
				WHERE MONTH(appointment_date) = ? 
				AND YEAR(appointment_date) = ?
				ORDER BY appointment_date";
		$query = $this->db->query($sql, array($month,$year));
		return $query->result();
	}
	
	function get_name_by_mr($key) {
		$sql = "SELECT nickname
				FROM tb_patient
				WHERE mr_no = ?
				LIMIT 1";
		$query = $this->db->query($sql,$key);
		$row = $query->row(1);
		return $row->nickname;
	}
	
	function long_format_date($date) {
		list($year,$month,$day) = explode("-",$date);
		return date("F j, Y",mktime(0,0,0,$month,$day,$year));
	}
	
	function convert_date_to_dayname($date) {
		list($year,$month,$day) = explode("-",$date);
		return date("l",mktime(0,0,0,$month,$day,$year));
	}
	
	function get_name_month($month) {
		switch ($month) {
			case "01": return "Januari";
			case "02": return "Februari";
			case "03": return "Maret";
			case "04": return "April";
			case "05": return "Mei";
			case "06": return "Juni";
			case "07": return "Juli";
			case "08": return "Agustus";
			case "09": return "September";
			case "10": return "Oktober";
			case "11": return "November";
			case "12": return "Desember";
		}
		return false;
	}
	
	function get_name_day($day) {
		switch ($day) {
			case 0 : return "Minggu";
			case 1 : return "Senin";
			case 2 : return "Selasa";
			case 3 : return "Rabu";
			case 4 : return "Kamis";
			case 5 : return "Jumat";
			case 6 : return "Sabtu";
		}
		return false;
	}
	
	function get_temporary_diagnosis($app_id) {
		$sql = "SELECT keluhan, temp_diagnosis
				FROM tb_appointment WHERE appointment_number = ?";
		$query = $this->db->query($sql,$app_id);
		if ($query->num_rows() > 0 )
			return $query->row();
		else
			return FALSE;
	}
	
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
	
	function get_patient_med_queue() {
		$sql = "SELECT x.appointment_number, x.mr_no, x.appointment_date, z.name, y.nickname, y.sex, x.other_doctor_name
				FROM tb_appointment as x, tb_patient as y, tb_doctor as z
				WHERE x.med_taken_status = 'N'
				AND y.mr_no = x.mr_no
				AND z.id = x.doctor_id";		
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0){
			return $query->result();
		} else
			return FALSE;
	}
	
	function insert_lagu($post) {
		$data = array(
						"nomor_lagu" => $post['nomor_lagu'],
						"judul_lagu" => $post['judul_lagu'],
						"isi_lagu" => $post['isi_lagu']
					);
		$sql = $this->db->insert_string('tb_lagu',$data);
		$query = $this->db->query($sql);
		if ($this->db->affected_rows() > 0)
			return TRUE;
		else
			return FALSE;
	}
	
	function fetch_lagu($id) {
		$sql = "SELECT * 
				FROM tb_lagu
				WHERE id = ?";
		$query = $this->db->query($sql, $id);
		if ($query->num_rows() > 0)
			return $query->row(1);
		else
			return FALSE;
		
	}
}	
