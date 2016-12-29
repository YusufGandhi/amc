<?php

class Payment extends Model {
	
	function Payment() {
		parent::Model();
	}
	
	function get_payment_details($app_id) {
		/*$sql = "SELECT SUM(harga) as total_paramita
				FROM tb_paramita_check_request
				WHERE id_app = ?";				
		$query = $this->db->query($sql, $app_id);
		$total_paramita = $query->row()->total_paramita;
		if ($total_paramita == "") $total_paramita = 0;
		$total_paramita *= 1.35; // for patient; add 135% from the normal price; this is the selling price of angsamerah
		//echo $total_paramita;exit;*/
		$sql = "SELECT x.administration_fee, x.doctor_fee, JUMLAH_HARGA_OBAT_BY_APP(z.appointment_number) as med_fee, x.proc_fee, (x.lab_fee + (x.paramita_fee * 1.35)) as lab_fee, x.amc_package_fee, z.appointment_date,
				y.salutation, CONCAT(y.first_name, ' ' ,y.middle_name, ' ', y.last_name) as full_name, y.nickname
				FROM tb_billing as x, tb_patient as y, tb_appointment as z
				WHERE y.mr_no = z.mr_no				
				AND z.appointment_number = x.id_appointment
				AND x.id_appointment = ?				
				LIMIT 1";
		$query = $this->db->query($sql, $app_id);
		/*$sql = "SELECT SUM(harga)
				FROM tb_paramita_check_request
				WHERE id_app =?";
		$query = $this->db->query($sql,$app_id);*/
		if($query->num_rows() > 0)
			return $query->row();
		else
			return FALSE;
	}
	
	function load_payment_method() {
		$query = $this->db->get('tb_payment_method');
		return $query->result();
	}
	
	function get_specimen() {
		$query = $this->db->get('tb_specimen');
		return $query->result();
	}
	
	function get_payment_method_patient($app_id) {
		$sql = "SELECT * FROM tb_payment WHERE appointment_id = ?";
		$query = $this->db->query($sql,$app_id);
		return $query->row();
	}

	
	function new_payment($no, $doctor, $admin) {
		$doctor_fee = $this->get_doctor_fee($doctor);
		if($admin)
			$admin_fee = $this->get_admin_fee($admin);
		else
			$admin_fee = 0;
		$data = array(
						'id_appointment' => $no,
						'doctor_fee' => $doctor_fee,
						'administration_fee' => $admin_fee
					);
		$this->db->insert("tb_billing",$data);
		if ($this->db->affected_rows() > 0)
			return TRUE;
		else
			return FALSE;
	}
	
	function generate_no_receipt($app_id) {
		$curr_year = date('y');
		$curr_month = $this->numberToRoman(date('n')); // convert the month into roman format
		$sql = "SELECT (MAX(SUBSTR(x.receipt_no,1,3)) + 1) as last_no
				FROM tb_payment as x
				WHERE SUBSTR(x.receipt_no,-2) = ?";
		$query = $this->db->query($sql,$curr_year);
		$no = $query->row(1)->last_no;
		if($no == "")
			$no = "001";
		else
			$no = $this->trailing_zero($no,3);
		$no .= "/AMC/$curr_month-$curr_year";
		//echo($no);
		$data = array(
						'receipt_no' => $no
					 );
		$where = "appointment_id = '".$app_id."'";
		$str = $this->db->update_string('tb_payment',$data,$where);
		$this->db->query($str);
		if ($this->db->affected_rows() > 0)
			return $no;
		else
			return FALSE;
		//exit;
	}
	
	
	// thanks to http://www.go4expert.com/forums/showthread.php?t=4948
	private function numberToRoman($num) {
	     // Make sure that we only use the integer portion of the value
	     $n = intval($num);
	     $result = '';
	 
	     // Declare a lookup array that we will use to traverse the number:
	     $lookup = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400,
	     'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40,
	     'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
	 
	     foreach ($lookup as $roman => $value) 
	     {
	         // Determine the number of matches
	         $matches = intval($n / $value);
	 
	         // Store that many characters
	         $result .= str_repeat($roman, $matches);
	 
	         // Substract that from the number
	         $n = $n % $value;
	     }
	 
	     // The Roman numeral should be built, return it
	     return $result;
	}
	
	private function trailing_zero($count, $length=5) {
		// first, check whether the variable $count
		// is numeric or not
		if (is_numeric($count)) {
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
	
	function save_payment($post) {
		$data = array (
						'appointment_id' => $post['app_id'],
						'payment_method' => $post['payment_method'],
						'card_type' => $post['card_type'],
						'card_number' => $post['card_number'],
						'date' => $post['payment_date'],
						'disc_percentage' => $post['discount'],
						'disc_amount' => $post['disc_amount']
					  );
		$this->db->insert('tb_payment',$data);
		$data = array (
						'paid' => 'Y'
					  );
		$this->db->where('appointment_number',$post['app_id']);
		$this->db->update('tb_appointment',$data);
	}
	
	function fetch_payment($type, $param1, $param2='') {
		// $type = 'D' -> DAILY ; 'M' -> MONTHLY
		// $param1; for D -> DATE, for M -> MONTH
		// $param2, for D -> null, for M -> YEAR
		
		// UPDATE FOR MARCH 3, 2011 paramita_fee * 1.35
		$sql = "SELECT x.appointment_id, x.receipt_no, t.method as payment_method, x.date, 
				y.doctor_fee, y.administration_fee, y.lab_fee, (1.35 * y.paramita_fee) as paramita_fee,
				y.proc_fee, JUMLAH_HARGA_OBAT_BY_APP(x.appointment_id) as med_fee, y.amc_package_fee, w.appointment_date as app_date,
				z.nickname, w.patient_type as status, w.service_procedure, v.name as nurse_name, w.other_doctor_name, u.name as doctor_name,
				x.disc_amount, x.disc_percentage
				FROM tb_payment_method as t, tb_doctor as u, tb_nurse as v, tb_appointment as w, tb_payment as x, tb_billing as y, tb_patient as z
				WHERE x.appointment_id = y.id_appointment
				AND x.appointment_id = w.appointment_number
				AND w.nurse_id = v.id
				AND u.id = w.doctor_id
				AND x.payment_method = t.id
				AND w.mr_no = z.mr_no";
		if ($type == 'D'){
			$sql .= " AND x.date = ?";
			$query = $this->db->query($sql, $param1);
		} else {
			$sql .= " AND MONTH(x.date) = ? AND YEAR(x.date) = ?";
			$query = $this->db->query($sql, array($param1,$param2));
		}
		
		if ($query->num_rows() > 0)
			return $query->result();
		else
			return FALSE;
	}
	
	function save_payment_details($post) {
		//print_r($post);exit;
		// UPDATING DATA FOR EXAMINATION DETAILS TO tb_appointment
		
		if (isset($post['paramita_price'])) {
			
			foreach( $post['paramita_price'] as $key => $value ) {
				$data = array (
								'id_app' => $post['app_id'],
								'id_pemeriksaan' => $key,
								'harga' => $value,
								'status' => 'N'
							  );
				$this->db->insert('tb_paramita_check_request',$data);
			}
		}
		
		$lab_result = ""; // lab result is for inserting the data for lab check field in tb_appointment
		if (isset($post['lab']) || isset($post['paramita_price'])) $lab_result = "N"; // IF THE LAB CHECK IS REQUESTED, INSERT 'N' = NEW; no longer 'P' = PENDING to the table
		$med_taken = isset($post['obat']) ? "N" : "";
		$data = array (
						'service_procedure' => $post['service_procedure'],
						'lab_check_status' => $lab_result,
						'main_complaint' => $post['main_complaint'],
						'anamnesa' => $post['anamnesa'],
						'blood_pressure' => $post['sistole']."/".$post['diastole'],
						'nadi' => $post['nadi'],
						'temperature' => $post['temperature'],
						'breath' => $post['breath'],
						'physical_notes' => $post['notes_physic'],
						'd_kerja' => $post['d_kerja'],
						'd_banding' => $post['d_banding'],
						'd_kerja' => $post['d_kerja'],
						'other_meds' => $post['other_meds'],
						'examined_status' => 'Y',
						'med_taken_status' => $med_taken,
						'package_id' => $post['package-id'],
						'rujukan_notes' => $post['rujukan_notes']
					  );
				
		$this->db->where('appointment_number',$post['app_id']);
		$this->db->update('tb_appointment',$data);
		
		// INSERTING DATA FOR PAYMENT to tb_billing
		// UPDATE 9 FEB 2011: PLEASE NOTE THAT LAB FEE IS NOT FIXED!!
		// ONCE AGAIN, LAB FEE IS NOT FIXED!!
		// STILL NEEDS TO ADD PARAMITA LAB PRICE
		
		
		
		
		if ($this->db->affected_rows() > 0) {
			
			// CHECK WHETHER THE LAB ITEM EXIST OR NOT
			if (isset($post['lab'])) {
				
				// IF EXIST, INSERTING LAB DETAILS TO THE TABLE 
				// TB_LAB_DETAILS_APP
				foreach ($post['lab'] as $key => $value) {
					$data_lab = array (
										'id_appointment' => $post['app_id'],
										'id_lab' => $key,
										'price' => $value,										
										'status' => 'N',
									  );
					$this->db->insert('tb_lab_details_app',$data_lab);
				}
			}
			
			if (isset($post['tindakan'])) {
				
				//IF EXISTS,  INSERTING MEDICAL PROCEDURE TO THE TABLE
				// TB_TINDAKAN_DETAILS_APP
				foreach ($post['tindakan'] as $key => $value) {
					$data_tindakan = array (
												'id_appointment' => $post['app_id'],
												'id_tindakan' => $key,
												'price' => $value
										   );
					$this->db->insert('tb_tindakan_details_app',$data_tindakan);
				}
			}
				
			$med_total_amount = 0;
			if (isset($post['obat'])) {
				// IF EXISTS, INSERTING MEDICINE TO THE TABLE
				// TB_OBAT_DETAILS_APP
				// UPDATE: 3 JAN 2012
				// THE PRICE IS NOT ADDED WITH 33% SINCE THE PRICE HAS BEEN ADDED
				foreach ($post['obat'] as $key => $value) {
					$data_obat = array (
												'id_appointment' => $post['app_id'],
												'id_obat' => $key,
												'amount' => (int) $post['amount'][$key],
												'dosis' => $post['dosis'][$key],
												'price' => $value
										   );
					$med_total_amount += ((int) $post['amount'][$key] * (int) $value);
					$this->db->insert('tb_obat_details_app',$data_obat);
				}
			}
			
			$data = array (
						'lab_fee' => (int) $post['price_lab'] ,
						'proc_fee' => (int) $post['price_tindakan'],
						'med_fee' => (int) $med_total_amount, // it used to be $post['price_lab']
						'paramita_fee' => (int) $post['total_price_paramita'],
						'amc_package_fee' => (int) $post['package-price']
					  );
					  
			if (isset($post['free_doctor_fee'])) {
				$data['doctor_fee'] = 0;
			}
			//the medical fee used to be this line
			//'med_fee' => (int) $post['price_obat'],
			// UPDATE 18 February 2011: for med fee, we use $med_total_amount
			// $med_total_amount is defined in the total obat
			$this->db->where('id_appointment',$post['app_id']);
			$this->db->update('tb_billing',$data);
					
			return TRUE;
		} else
			return FALSE;
	}
	// 085214048994
	function get_admin_fee ($id) {
		$sql = "SELECT x.price
				FROM tb_administrasi as x
				WHERE x.type = ?";
		$query = $this->db->query($sql, $id);
		if ($query->num_rows() > 0)
			return $query->row(1)->price;
		else 
			return FALSE;
	}
	
	function get_admin_desc ($app_id) {
		$sql = "SELECT x.jenis_administrasi
				FROM tb_administrasi as x, tb_appointment as y
				WHERE y.patient_type = x.type
				AND y.appointment_number = ?";
		$query = $this->db->query($sql, $app_id);
		if ($query->num_rows() > 0)
			return $query->row(1)->jenis_administrasi;
		else
			return FALSE;
	}
	
	function get_doctor_fee($id) {
		$sql = "SELECT x.price
				FROM tb_doctor_price as x
				WHERE x.type = ?";
		$query = $this->db->query($sql,$id);
		
		if ( $query->num_rows() > 0 )
			return $query->row(1)->price;
		else
			return FALSE;
	}
	
	function get_doctor_desc($app_id) {
		$sql = "SELECT x.desc
				FROM tb_doctor_price as x, tb_appointment as y, tb_doctor as z
				WHERE x.type = z.type
				AND z.id = y.doctor_id
				AND y.appointment_number = ?";
		$query = $this->db->query($sql,$app_id);
		
		if ( $query->num_rows() > 0 )
			return $query->row(1)->desc;
		else
			return FALSE;
	}
	
	function get_patient_examined($stat, $id=-1) {
		$sql = "SELECT x.appointment_number, x.mr_no, x.appointment_date, z.name, y.nickname, y.sex, x.other_doctor_name
				FROM tb_appointment as x, tb_patient as y, tb_doctor as z
				WHERE x.examined_status = ? ";
		if ($stat == 'Y') $sql .= "AND x.paid <> 'Y' ";
		$sql .= "AND x.mr_no = y.mr_no
				AND x.doctor_id = z.id";
		if ($id != -1) {
			$sql .= " AND z.id = ?";
			$data = array($stat, $id);
		} else {
			$data = $stat;
		}
		$query = $this->db->query($sql, $data);
		if ($query->num_rows() > 0){
			return $query->result();
		} else
			return FALSE;
	}
	
	function get_appointment_details($id) {
		$sql = "SELECT y.first_name, y.middle_name, y.last_name, y.salutation
				FROM tb_appointment as x, tb_patient as y
				WHERE x.mr_no = y.mr_no
				AND x.appointment_number = ?";
		$query = $this->db->query($sql,$id);
		if ($query->num_rows() > 0)
			return $query->row(1);
		else
			return FALSE;
	}
	
	function get_jumlah_harga_obat($app_id) {
		$sql = "SELECT jumlah_harga_obat_by_app(id_appointment) as jumlah
				FROM tb_log_stock_obat
				WHERE id_appointment = ?";
		$query = $this->db->query($sql,$app_id);
		if ($query->num_rows() > 0)
			return $query->row(1)->jumlah;
		else
			return 0;
	}
	/*
	<input type="button" name="button" value="Pay now!" style="background-repeat:no-repeat;border-width:0px;background-color:transparent;height:25px;width:90px;cursor:hand;font-weight:bold;font-size:11px;font-family:Arial;color:#FFFF33;background-image:url('ROvlRed.gif');" onClick="window.location.href('http://www.unai.edu')">
	
	*/
	
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
				FROM tb_laboratorium
				ORDER BY id";
		$query = $this->db->query($sql);
		return $query->result();
	}
	
	function get_obat() {
		$sql = "SELECT *
				FROM tb_obat";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0)
			return $query->result();
		else
			return FALSE;
	}
	
	
	/*
	  * RETURN TRUE
	  * if the paid == Y
	  * 
	  */
	function check_paid_status($app_id) {
		$this->db->select("paid");
		$query = $this->db->get_where("tb_appointment",array('appointment_number' => $app_id));
		if( $query->num_rows() > 0 ) {
				if ( $query->row(1)->paid == "Y" )
					return TRUE;					
		}
		return FALSE;
	}
	
}