<?php

class Medical extends Model {
	
	function Medical() {
		parent::Model();
	}
	
	function previous($app_id) {
		$sql = "SELECT appointment_date, main_complaint, anamnesa, blood_pressure,
				nadi, temperature, breath, physical_notes, d_kerja, d_banding,
				other_meds, rujukan_notes, package_id
				FROM tb_appointment
				WHERE appointment_number = ?";
		$query = $this->db->query($sql,$app_id);
		if ( $query->num_rows() > 0 )
			return $query->row();
		else
			return FALSE;
	}
	
	function search_patient_data_lab($keyword, $lab_stat="D") {
		$sql = "SELECT DISTINCT(x.mr_no) as mr_no, x.nickname
				FROM tb_patient as x, tb_appointment as y
				WHERE (x.nickname LIKE '%". $keyword ."%' OR x.mr_no LIKE '%". $keyword ."%')
				AND x.mr_no = y.mr_no
				AND y.lab_check_status = ?";
		$query = $this->db->query($sql, $lab_stat);
		return $query->result();
		//return FALSE;
	}
	
	function detil_tindakan($app_id) {
		$sql = "SELECT y.tindakan
				FROM tb_tindakan_details_app as x, tb_tindakan as y
				WHERE x.id_appointment = ?
				AND x.id_tindakan  = y.id";
		$query = $this->db->query($sql,$app_id);
		if ($query->num_rows() > 0 )
			return $query->result();
		else
			return FALSE;
	}
	
	function detil_lab($app_id,$stat='') {
		$sql = "SELECT y.type, y.id, x.result, z.specimen
				FROM tb_lab_details_app as x, tb_laboratorium as y, tb_specimen as z
				WHERE x.id_appointment = ?
				AND y.specimen = z.id
				AND x.id_lab  = y.id";
		if ($stat != '') $sql .= " AND x.status = '".$stat."'";
		$sql .= " ORDER BY y.specimen";
		$query = $this->db->query($sql,$app_id);
		if ($query->num_rows() > 0 )
			return $query->result();
		else
			return FALSE;
	}
	
	function detil_lab_paramita($app_id,$stat='') {
		$sql = "SELECT y.pemeriksaan, y.id, x.result
				FROM tb_paramita_check_request as x, tb_paramita as y
				WHERE x.id_app = ?
				AND x.id_pemeriksaan  = y.id";
		if ($stat != '') $sql .= " AND x.status = '".$stat."'";
		$query = $this->db->query($sql,$app_id);
		if ($query->num_rows() > 0 )
			return $query->result();
		else
			return FALSE;
	}
	
	function get_specimen() {
		$query = $this->db->get('tb_specimen');
		return $query->result();
	}
	
	function result_lab_type($id) {
		$sql = "SELECT y.result_type
				FROM tb_laboratorium as x, tb_lab_result_type as y
				WHERE x.id = ?
				AND x.result_type = y.id";
		$query = $this->db->query($sql,$id);
		if ($query->num_rows() > 0)
			return $query->row()->result_type;
		else
			return FALSE;
	}
	
	function list_item_lab($id) {
		$sql = "SELECT x.item, x.order_list
				FROM tb_lab_result_fixed as x, tb_laboratorium as y
				WHERE x.id = y.id_fixed
				AND y.id = ?
				ORDER BY x.order_list ASC";
		$query = $this->db->query($sql,$id);		
		return $query->result();
		
	}
	
	function get_new_lab_check_patient($stat) {
		$sql = "SELECT x.appointment_number, x.mr_no, x.appointment_date, x.other_doctor_name, z.name, y.nickname, y.sex
				FROM tb_appointment as x, tb_patient as y, tb_doctor as z
				WHERE x.lab_check_status = ?
				AND x.mr_no = y.mr_no
				AND x.doctor_id = z.id";
		$query = $this->db->query($sql, $stat);
		if ($query->num_rows() > 0){
			return $query->result();
		} else
			return FALSE;
	}
	
	function detil_obat($app_id) {
		$sql = "SELECT y.nama_obat, x.amount, x.dosis, y.unit, y.jenis
				FROM tb_obat_details_app as x, tb_obat as y
				WHERE x.id_appointment = ?
				AND x.id_obat  = y.id";
		$query = $this->db->query($sql,$app_id);
		if ($query->num_rows() > 0 )
			return $query->result();
		else
			return FALSE;
	}
	
	function search_paramita($key) {
		$sql = "SELECT *
				FROM tb_paramita
				WHERE pemeriksaan LIKE '%".$key."%'
				LIMIT 10";
		$query = $this->db->query($sql);
		return $query->result();
	}
	
	
	function get_tindakan() {
		$sql = "SELECT *
				FROM tb_tindakan";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0)
			return $query->result();
		else
			return FALSE;
	}
	
	function get_lab() {
		$sql = "SELECT *
				FROM tb_laboratorium";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0)
			return $query->result();
		else
			return FALSE;
	}
	
	function get_obat($use_keyword = FALSE, $keyword="",$jenis="") {
		$sql = "SELECT *
				FROM tb_obat";
		if ($use_keyword == TRUE && $keyword != "")
			$sql .= " WHERE nama_obat LIKE '%".$keyword."%'";
		if ($jenis != "")
			$sql .= " AND jenis='".$jenis."'";
		if ($use_keyword != FALSE)
			$sql .= " LIMIT 10";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0)
			return $query->result();
		else
			return FALSE;
	}
	
	function get_data_obat($num, $offset) {
		$query = $this->db->get('tb_obat', $num, $offset);	
	    return $query->result();
	}

	
	function save_lab_check($post) {
		$date = date("Y-m-d H:i:s");
		//print_r($post);exit;
		$i = 1;
		if (isset($post['lab_value'])) {
			for (; $i <= count($post['lab_value']); $i++) {
				if ($post['lab_value'][$i] == "Pending") $stat = "P";
				else $stat = "D";
				$result = $post['lab_value'][$i];
				//print_r($result);exit;
				$data = array ( 
								'status' => $stat,
								'result' => $result,
								'result_entry_date' => $date,
								'result_entry_by' => $this->session->userdata('name')
							  );
				$where = "id_appointment = '". $post['app_id'] ."' AND id_lab = '". $post['check'][$i] ."'";
				$str = $this->db->update_string('tb_lab_details_app',$data,$where);
				$this->db->query($str);
			}
		}
		if (isset($post['paramita_lab_value'])) {
			for ($j = 1 ; $j <= count($post['paramita_lab_value']); $i++, $j++) {
				if ($post['paramita_lab_value'][$j] == "Pending") $stat = "P";
				else $stat = "D";
				$result = $post['paramita_lab_value'][$j];
				//print_r($result);exit;
				$data = array ( 
								'status' => $stat,
								'result' => $result,
								'result_entry_date' => $date,
								'result_entry_by' => $this->session->userdata('name')
							  );
				$where = "id_app = '". $post['app_id'] ."' AND id_pemeriksaan = '". $post['check'][$i] . "'";
				$str = $this->db->update_string('tb_paramita_check_request',$data,$where);
				$this->db->query($str);
			}
		}
		$data = array ( 'lab_check_status' => $post['check_status'] );
		$where = "appointment_number = '".$post['app_id']."'";
		$str = $this->db->update_string('tb_appointment',$data,$where);
			$this->db->query($str);
		//if ($this->db->affected_rows() > 0)
		return $post['check_status'];
		
		
	}
	
	function get_lab_result($app_id, $HIV) {
		$sql = "SELECT x.type, y.result, y.result_entry_by, w.specimen, w.id as id_specimen
				FROM tb_laboratorium as x, tb_lab_details_app as y, tb_appointment as z, tb_specimen as w
				WHERE x.id =  y.id_lab
				AND w.id = x.specimen
				AND y.id_appointment = ?
				AND z.appointment_number = y.id_appointment";
		if ($HIV) $sql .= " AND x.type = 'HIV'";
		else $sql .= " AND x.type<>'HIV'";
		$query = $this->db->query($sql,$app_id);
		return $query->result();
	}
	
	function get_all_lab_result($app_id) {
		$sql = "SELECT x.type, w.specimen, y.result, y.result_entry_by
				FROM tb_specimen as w, tb_laboratorium as x, tb_lab_details_app as y, tb_appointment as z
				WHERE x.id =  y.id_lab
				AND x.specimen = w.id
				AND y.id_appointment = ?
				AND z.appointment_number = y.id_appointment";
		$query = $this->db->query($sql,$app_id);
		return $query->result();
	}
	
	function has_HIV($app_id) {
		$sql = "SELECT y.type
				FROM tb_lab_details_app as x, tb_laboratorium as y
				WHERE x.id_appointment = ?
				AND x.id_lab = y.id
				AND y.type = 'HIV'";
		$query = $this->db->query($sql,$app_id);
		if($query->num_rows() > 0)
			return TRUE;
		else
			return FALSE;
	}
	
	function update_stock_obat($post) {
		//print_r($post);exit;
		if (isset($post['operasi'])) {
			if ($post['operasi'] == "I") {
				
					$data = array (
									'id_obat' => $post['id_obat'],
									'amount' => $post['jumlah_masuk'],
									'price' => $post['harga_baru'],
									'operation_date' => date("Y-m-d H:i:s"),
									'operation' => $post['operasi'],
									'admin' => $post['admin'],									
									'keterangan' => $post['keterangan']
								  );
				
				//print_r($data);exit;
				/*$sql = $this->db->insert_string("tb_log_stock_obat_masuk",$data);
				$query = $this->db->query($sql);
				if ($this->db->affected_rows() > 0)
					return TRUE;*/
			} else {
				//print_r($_POST);exit;
				if (isset($post['id'])) {
					foreach($post['id'] as $key => $val) {
						$data = array (
									'id_obat' => $key,
									'amount' => $post['jumlah'][$key],
									'price' => $post['price'][$key],
									'operation_date' => date("Y-m-d H:i:s"),
									'operation' => $post['operasi'],
									'admin' => $post['admin'],
									'id_appointment' => $post['app_id']
									
								  );
						$sql = $this->db->insert_string('tb_log_stock_obat',$data);
						$query = $this->db->query($sql);
						
					}
					$data = array( 'med_taken_status' => 'Y' );
					$where = "appointment_number = '".$post['app_id']."'";
					$sql = $this->db->update_string('tb_appointment',$data,$where);
					$query = $this->db->query($sql);
					if ($this->db->affected_rows() > 0)
						return TRUE;
					else
						return FALSE;
				} else {
				
					$data = array (
									'id_obat' => $post['id_obat'],
									'amount' => $post['jumlah_keluar'],
									'price' => $post['harga_jual'],
									'operation_date' => date("Y-m-d H:i:s"),
									'operation' => $post['operasi'],
									'admin' => $post['admin'],
									'keterangan' => $post['keterangan']
								  );
				}
				/*$sql = $this->db->insert_string('tb_log_stock_obat_keluar',$data);
				$query = $this->db->query($sql);
				if ($this->db->affected_rows() > 0)
					return TRUE;
				else
					return FALSE;*/
			
			}		
			$sql = $this->db->insert_string('tb_log_stock_obat',$data);
			$query = $this->db->query($sql);
			if ($this->db->affected_rows() > 0)
				return TRUE;
			else
				return FALSE;
		}
	}
	
	function report_obat_keluar($post) {
		
		// SELECT id_obat, saldo awal bulan, saldo akhir bulan
		
		// previously
		$sql = "SELECT DISTINCT(id_obat), nama_obat, current_saldo_obat_month(id_obat,".($post['report_month'] - 1) .",".$post['report_year'].") as saldo_awal_bulan, current_saldo_obat_month(id_obat,".$post['report_month'] .",".$post['report_year'].") as saldo_akhir_bulan
				FROM tb_log_stock_obat as x, tb_obat as y
				WHERE MONTH(operation_date) = ?
				AND YEAR(operation_date) = ?
				AND x.id_obat = y.id
				AND x.operation = 'O'";
		
		/*$sql = "SELECT DISTINCT(id_obat), nama_obat, current_saldo_obat_month(id_obat,".($post['report_month'] - 1) .",".$post['report_year'].") as saldo_awal_bulan, current_saldo_obat_month(id_obat,".$post['report_month'] .",".$post['report_year'].") as saldo_akhir_bulan
				FROM tb_log_stock_obat_keluar as x, tb_obat as y
				WHERE MONTH(operation_date) = ?
				AND YEAR(operation_date) = ?
				AND x.id_obat = y.id ";*/
		$query = $this->db->query($sql, array($post['report_month'], $post['report_year']));
		if ($query->num_rows() > 0)
			return $query->result();
		else
			return FALSE;
			
	}
	
	function detil_obat_keluar_per_item($id, $post) {
		$sql = "SELECT CONCAT(DAY(operation_date),'/',MONTH(operation_date),'/',YEAR(operation_date)) as operation_date, amount, price, current_stock_based_on_date($id, operation_date) as sisa, tb_appointment.mr_no as mr_no
				FROM tb_log_stock_obat LEFT JOIN tb_appointment
				ON tb_log_stock_obat.id_appointment = tb_appointment.appointment_number
				WHERE id_obat = ?				
				AND operation = 'O'
				AND MONTH(operation_date) = ?
				AND YEAR(operation_date) = ?
				ORDER BY operation_date";
		$query = $this->db->query($sql, array($id, $post['report_month'], $post['report_year']));
		return $query->result();
		
	}
	
	function add_data_obat($post) {
		$data = array(
						'nama_obat' => $post['nama_obat'],
						'jenis' => $post['jenis_obat'],
						'unit' => $post['unit_obat']
						
					 );
		
		$sql = $this->db->insert_string('tb_obat', $data);
		$query = $this->db->query($sql);
		if ($this->db->affected_rows() > 0) {
			$post['id_obat'] = $this->db->insert_id();
			return $this->update_stock_obat($post);			
		}
		return FALSE;
		
	}
	
	function check_obat_exist($id) {
		$query = $this->db->get_where('tb_obat',array('id'=>$id));
		if ($query->num_rows() > 0)
			return $query->row(1);
		else
			return FALSE;
	}
	
	function update_data_obat($post) {
		$data = array (
						"nama_obat" => $post['nama_obat'] ,
						"unit" => $post['unit_obat'],
						"jenis" => $post['jenis_obat']
					  );
		$where = "id = ".$post['id_obat']." ";
		$sql = $this->db->update_string('tb_obat',$data,$where);
		$query = $this->db->query($sql);
		if ($this->db->affected_rows() > 0)
			return TRUE;
		else
			return FALSE;
		
	}
	
	function lab_done_per_month($post) {
		$sql = "SELECT DISTINCT(id_lab), y.type, z.specimen
				FROM tb_lab_details_app as x, tb_laboratorium as y, tb_specimen as z
				WHERE x.id_lab = y.id
				AND y.specimen = z.id
				AND MONTH(result_entry_date) = ?
				AND YEAR(result_entry_date) = ?";
		$query = $this->db->query($sql, array($post['report_month'],$post['report_year']));
		if ($query->num_rows() > 0)
			return $query->result();
		else
			return FALSE;
	}
	
	function detil_lab_done_per_item($id, $post) {
		$sql = "SELECT result, mr_no,
				CONCAT(DAY(result_entry_date),'/',MONTH(result_entry_date),'/',YEAR(result_entry_date)) as date
				FROM tb_lab_details_app as x LEFT JOIN tb_appointment as y
				ON x.id_appointment = y.appointment_number
				WHERE id_lab = ?				
				AND MONTH(result_entry_date) = ?
				AND YEAR(result_entry_date) = ?
				ORDER BY result_entry_date";
		$query = $this->db->query($sql, array($id, $post['report_month'], $post['report_year']));
		return $query->result();
	}
	
	function patient_lab_checked_per_month($post) {
		$sql = "SELECT DISTINCT(x.id_appointment), y.mr_no
				FROM tb_lab_details_app as x, tb_appointment as y
				WHERE x.id_appointment = y.appointment_number				
				AND MONTH(result_entry_date) = ?
				AND YEAR(result_entry_date) = ?";
		$query = $this->db->query($sql, array($post['report_month'],$post['report_year']));
		if ($query->num_rows() > 0)
			return $query->result();
		else
			return FALSE;
	}
	
	function detil_lab_checked_per_appointment($id) {
		$sql = "SELECT x.id_lab, y.type, z.specimen, x.result
				FROM tb_lab_details_app as x, tb_laboratorium as y, tb_specimen as z
				WHERE x.id_lab = y.id
				AND y.specimen = z.id
				AND id_appointment = ?";
		$query = $this->db->query($sql, $id);
		if ($query->num_rows() > 0)
			return $query->result();
		else
			return FALSE;
	}
	
	function detil_lab_pramita_checked_per_appointment($id) {
		$sql = "SELECT y.pemeriksaan, y.klasifikasi, x.result
				FROM tb_paramita_check_request as x, tb_paramita as y
				WHERE x.id_app = ?
				AND x.id_pemeriksaan = y.id";
		$query = $this->db->query($sql, $id);
		if ($query->num_rows() > 0)
			return $query->result();
		else
			return FALSE;
	}
	
}