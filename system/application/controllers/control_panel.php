<?php

class Control_panel extends Controller {

	function Control_panel()
	{
		parent::Controller();
		$this->load->helper('url');
		$this->load->helper('form');
		$this->load->library('session');		
		if ($this->session->userdata('logged_in') != TRUE || $this->session->userdata('station') != '1') {
				redirect('/welcome');
		}
		$this->load->model('appointment','',TRUE);
		
	}
	
	
	function index()
	{
		
		$data['baseURL'] = base_url();
		$data['title'] = "Control Panel Station ".$this->session->userdata('station')." - ".$this->session->userdata('name');
		$data['custom_message'] = "<h2>Welcome to Control Panel Station ".$this->session->userdata('station')."</h2><br />Please select the operation you need in the menu to your left.";
		
		
		$this->load->view('custom_message',$data);
		
	}
	
	function new_registration() {
		$this->load->model('patient','',TRUE);
		$data['new_patient'] = TRUE;
		$data['form_validator'] = TRUE;
		$data['baseURL'] = base_url();
		$data['title'] = "New Patient Registration - Please input the patient's details";
		$data['menu_1_new_patient'] = TRUE;
		//$curr_year = date('Y');
		//$data['mr_no'] = $curr_year.".01.".$this->trailing_zero($this->patient->count_existing_patient_year($curr_year));
		$this->load->view('new_patient',$data);
	}	
	
	function check_unique_no() {
		$unique_no = $this->input->post('tx_unique');		
		$this->load->model('patient','',TRUE);
		$exist = $this->patient->check_existing_unique($unique_no);
		if ($this->input->post('tx_unique') == "")
			echo "empty";
		else if ($exist)
			echo "yes";
		else
			echo "no";
	}
	
	/*function update_appointment_details() {
		$this->
	}*/
	
	function save_appointment() {
		if (isset($_POST['date'])) {
			$data['baseURL'] = base_url();
			$patient_type = $_POST['patient_type'];
			
			/* UPDATE: AUGUST 23, 2011
			  * FREE ADMINISTRATION CHECK MARK
			  * IF CHECKED AND BY DEFAULT 
			  */
			if (isset($_POST['free_admin'])) {
				$patient_type = FALSE;
			}
			
			if($patient_type == "NP" || $patient_type == "NC")
				$data['menu_1_new_patient'] = TRUE;
			else
				$data['menu_1_returning_patient'] = TRUE;
				
			$app_id = $this->appointment->insert_appointment($_POST);
			if ($app_id) {
				$this->load->model('payment','',TRUE);
				
				
				// if patient type is NP but it has couple (app_id[1] exists
				// changed no NC (new couple)
				/*if ($patient_type == "NP" && isset($app_id[1]))
					$patient_type = "NC";
				else if($patient_type == "RP" && isset($app_id[1]))
					$patient_type = "RC";*/
					
				$this->payment->new_payment($app_id[0], $_POST['doc_type'], $patient_type);
				if (isset($app_id[1])) {
					$this->load->model('patient','',TRUE);
					$this->payment->new_payment($app_id[1], $_POST['doc_type'], FALSE);
					$row = $this->patient->get_patient_details($app_id[1]);
					$couple_name = $row->nickname;
				}
				//list($hour, $room) = explode('-',$this->input->post('availability'));
				$data['title'] = "Appointment saved";
				$msg = "<div class='success'>Appointment successfully created</div>";
				$msg .= "<br /><br /><table>";
				// DIRECT IMPACT OF THE CHANGE OF THE COUPLE APPOINTMENT
				$msg .= "<tr><td>Name</td><td>:</td><td><strong>".$this->appointment->get_name_by_mr($this->input->post('mr_no'))."";
				$msg .= isset($couple_name) ? " & $couple_name" : "";
				$msg .= "</strong></td></tr>";
				// END OF DIRECT IMPACT				
				$msg .= "<tr><td>Day / Date - Time</td><td>:</td><td><strong>".$this->appointment->convert_date_to_dayname($this->input->post('date'))." / ".$this->appointment->long_format_date($this->input->post('date'))." - ". $this->appointment->get_hour_by_id($this->input->post('dd_list_hour_start')) ." - ".$this->appointment->get_end_hour_by_id($this->input->post('dd_list_hour_end'))."</strong></td></tr>";
				$msg .= "<tr><td>Doctor</td><td>:</td><td><strong>".$this->appointment->get_doctor_name($this->input->post('dd_list_doctor')).($this->input->post('other_name')==""?'':" (".$this->input->post('other_name').")")."</strong></td></tr>";
				$msg .= "<tr><td>Room</td><td>:</td><td><strong>".$this->input->post('dd_list_room')."</strong></td></tr>";
				$msg .= "</table>";
			} else {
				$title = "Failed";
				$msg = "Gagal";
			}
			$data['custom_message'] = $msg;
			$this->load->view('custom_message',$data);
		} else {
			redirect('/control_panel');
		}
	}
	
		
	function generate_doctor_schedule() {		
			$doctor = $this->appointment->get_doctor_list();
			$room = $this->appointment->get_room_list();
			$hour = $this->appointment->get_hour_list();
			echo "<script type=\"text/javascript\">";
			/*echo "$(\"td.available\").hover( 
					function() {					
						$(this).addClass(\"td-highlight\");
					}, 
					function() {
						$(this).removeClass(\"td-highlight\");
					}			
				);
			";
			echo "$(\"td.available\").click(
					function() {
						$(\"td.available\").removeClass(\"td-selected\");
						$(this).addClass(\"td-selected\");
						$(this).find(\"input:radio\").attr(\"checked\",\"checked\");						
					}
				);
				
				
			";*/
			//echo "	higlight_schedule();";
			echo "</script>";
			echo "<span id=\"title\">".$this->appointment->get_doctor_name($_POST['doctor_id'])."'s schedule on ". $this->appointment->long_format_date($_POST['date']) ."</span>";
			//echo "<!-- THIS IS THE TABLE TO FILL IN THE SCHEDULE OF HOUR, ROOM AVAILABILITY -->";
			echo "<table class=\"tb_schedule\">";
			
			//echo "	<!-- TABLE HEADER -->";
			echo "	<tr>";
			echo "		<th rowspan=\"2\">Time</td>";
			echo "		<th colspan=\"".count($room)."\">Room</td>";
			echo "	</tr>";
			/*echo "	<!-- END OF TABLE HEADER -->";
				
			echo "	<!-- ***************************************";
			echo "	THIS IS THE SPACE TO LOOP BETWEEN ROOM IN DB ";
			echo "	********************************************";
			echo "	-->";*/
			echo "	<tr>";			
			
			foreach ($room as $row) {
				echo "		<th>$row->room_number</th>";
			} 
			echo "	</tr>";
			//echo "	<!-- END OF THE ROOM SPACE -->";
				
				
			/*echo "	<!-- *******************************";
			echo "	THIS IS THE SPACE TO LOOP BETWEEN HOUR ";
			echo "	************************************";
			echo "	-->";*/
			$i=0;
			
			// THIS IS FOR THE ROWSPANNING PURPOSES
			// IF THE DOCTOR PRACTICE'S HOUR BLOCK MORE THAN 1 SLOT OF TIME
			$rowspan = 1;
			$rowspan = 1;
			$room_rowspan = array();
			$j=0;
			// SETTING ROWSPAN TO DEFAULT NUMBER: 1 FOR EACH ROOM
			// 1 Means NO ROWSPAN
			foreach ($room as $key) {
				$room_rowspan[$j] = 1;
				$j++;
			}
			$class = 'odd';
			
			// this is the variable to check the status span
			// if it's true then the rowspan of a doctor scheduling block is mode ON
			//$status_span = FALSE;
			//$last_row_span = FALSE;
			//$active_doctor = FALSE;
			//$room_index = FALSE;
			$rowspan_current = 0;
			foreach ($hour as $row) {
				
				echo "	<tr class=\"$class\">";
				//    PRINTING 1ST COLUMN: HOUR
				echo "		<td style=\"height: 20px;\">$row->hour - $row->end</td>";
				/*            END OF 1st COLUMN 
				
				---  TRICKY PART: CHECK WHETHER THE SELECTED DOCTOR ALREADY GOT APPOINTMENT IN THAT PARTICULAR HOUR
				---		IF YES, BLOCK ALL THE AVAILABILITY SLOT, SO IT CANNOT BE CHOSEN
				---		IF NO, PRINT AVAILABILITY ONE BY ONE
				---
				---		UPDATE : June 22, 2010    		
				---		blocking is no longer using colspan, but just printing not available in the cell but, first check that the current
				---		hour is blocked by the selected doctor in the doctor's dropdown list
				---		if yes; then check how many hour slot is blocked by the curent doctor
				---   	by substracting the end of appointment hour and the start hour
				-->		 */
				
				$exist_current = $this->appointment->check_existing_appointment_doctor($_POST['date'],$row->id,$_POST['doctor_id']);
				if ($exist_current) {
					//print_r($exist_current_doctor);
					$start_current = (int)$exist_current->id_hour;
					$end_current = (int)$exist_current->id_hour_end;
					$rowspan_current = $end_current - $start_current + 1;					
				}
				
				foreach ($room as $row2) {
					
					//  check if there's appointment in the room for the particular hour
					//  if appointment exist: then print the doctor and patient unique number
					$result = $this->appointment->check_existing_appointment($_POST['date'],$row2->id,$row->id);
					$index = (int) $row2->id - 1; //<-- $index is for recording the room id
					if($result) {
						$start = (int)$result->id_hour;
						$end = (int)$result->id_hour_end;
						
						// check if the appointment slot hour is more than 1 by comparing the end time and 
						// and the beginning time. If yes, then take the room->id as the index for room rowspan
						// if not, just print the cell without rowspan
						if ( $start < $end ) {									
							$room_rowspan[$index] = $end - $start + 1;
							echo "			<td rowspan=\"$room_rowspan[$index]\" style=\"text-align: center; width:90px; padding:0 3px;background: #696969;color: #fff;\">";
						}
						else 
							echo "			<td style=\"text-align: center; width:90px; padding:0 3px;background: #696969;color: #fff;\">";
						echo $this->appointment->get_doctor_name($result->doctor_id);//."<br />Patient:". $result->mr_no;							
					
					// this is the else for $result 
					} else {				
						
						//  check if there's running current rowspan in active room
						//  if yes then just decrease by 1 the rowspan value
						//  so, it doesn't print the cell
						if ($room_rowspan[$index] > 1) {
							$room_rowspan[$index] -= 1;								
						
						} else {
							// check if there's an active doctor (signed by $rowspan_current) running in the current cell
							// if yes then print Not available in the current cell
							// if not print available
							if ($rowspan_current) {
								echo "			<td style=\"text-align: center; width:90px; padding:0 3px;\" class=\"td-highlight\">";
								echo "Not available";								
							} else {
								echo "			<td style=\"text-align: center; width:90px; padding:0 3px;\" class=\"available\">";
								echo "			<div id=\"sch_$row->id-$row2->id\" style=\"display:block;\">Available</div>";
							}
						}							
					}
											
					echo "		</td>";
				}			
				$class=='even' ? $class='odd':$class='even';	
				echo "	</tr>";
				if($rowspan_current) $rowspan_current--;
			}
					 
				
			//	<!-- END OF LOOP BETWEEN HOUR -->
				
			echo "</table>";
	}
	
	function check_existing_appointment_doctor() {
		$result = $this->appointment->check_existing_appointment_doctor($_POST['date'],$_POST['hour'],$_POST['doctor']);
		if ($result)
			echo "yes";
		else
			echo "no";
		/*echo "Date: ".$_POST['date']."\nRoom: ".$_POST['room']."\nHour: ".$_POST['hour']."\n Doctor: ".$_POST['doctor'];*/
	}
	
	// *** FUNCTION FOR RETURNING PATIENT REGISTRATION
	function rp_registration() {
		$data['baseURL'] = base_url();
		$data['returning_patient'] = TRUE;
		$data['form_validator'] = TRUE;
		$data['menu_1_returning_patient'] = TRUE;
		$data['title'] = "Returning Patient Registration";
		$this->load->view('returning_patient',$data);
	}
	
	function confirm_arrival() {
		if(isset($_POST['id_appointment'])) {
			$data['baseURL'] = base_url();
			$data['menu_1_patient_arrival'] = TRUE;
			// Change the appointment status from "P" -pending to "A" - Arrived
			// and examined status to "N" - not yet examined
			$update = $this->appointment->update_appointment_status($_POST['id_appointment'], "A");
			if ($update) {
				$data['custom_message'] = "<div class='success'>Ready to serve!</div>";
			} else {
				$data['custom_message'] = "Failed";
			}
			$this->load->view('custom_message',$data);
		} else
			redirect('/control_panel');
	}
	
	function cancel_appointment() {
		$data['menu_1_cancel_appointment'] = TRUE;
		if(isset($_POST['id_appointment'])) {
			$data['baseURL'] = base_url();
			// Change the appointment status from "P" -pending to "C" - Cancelled
			$update = $this->appointment->update_appointment_status($_POST['id_appointment'], "C");
			if ($update) {
				$data['custom_message'] = "<div class='success'>Appointment cancelled</div>";
			} else {
				$data['custom_message'] = "Failed";
			}
			$this->load->view('custom_message',$data);
		} else {
			$data['baseURL'] = base_url();
			$data['title'] = "Appointment Cancellation";
			
			// the cancelling appointment function using the same jquery function with patient arrival
			// that's why we enable "patient arrival" jquery function
			$data['patient_arrival'] = TRUE;
			$data['target_url'] = site_url('/control_panel/cancel_appointment');
			$this->load->view('appointment_cancellation',$data);
		}
	}
	
	function edit_appointment() {
		$data['baseURL'] = base_url();
		$data['title'] = "Appointment Edit";
		$data['menu_1_edit_appointment'] = TRUE;
		// the edit appointment function using the same jquery function with patient arrival
		// that's why we enable "patient arrival" jquery function
		$data['patient_arrival'] = TRUE;
		$data['submit_url'] = "edit_appointment_details";
		$data['page_title'] = "Edit Patient Appointment Data";
		$data['button_value'] = "Go to appointment data";
		// UPDATE: patient_arrival will be used as the view for the beginning of appointment
		$this->load->view('patient_arrival',$data);
	}
	
	function edit_appointment_details() {
		if(isset($_POST['id_appointment'])) {
			$this->load->model('appointment','',TRUE);
			//$data['doctor'] = $this->appointment->get_doctor_list('R');
			$data['baseURL'] = base_url();
			$data['menu_1_edit_appointment'] = TRUE;
			$data['room'] = $this->appointment->get_room_list();
			$data['hour'] = $this->appointment->get_hour_list();
			$data['nurse'] = $this->appointment->get_nurse_list();
			$data['datepicker'] = TRUE;
			$data['update_but'] = TRUE;
			$data['appointment'] = $this->appointment->get_details_appointment($_POST['id_appointment']);
			$data['doctor'] = $this->appointment->get_doctor_list($data['appointment']->type);
			if ($data['appointment']->couple_appointment_id !== NULL) {
				$data['couple_mr_no'] = $this->appointment->get_mr_no_by_appointment_id($data['appointment']->couple_appointment_id);
			}
			//print_r($data);exit;
			$this->load->view('edit_appointment',$data);
		} else
			redirect('/');
	}
	
	function save_update() {
		if(isset($_POST['app_id'])) {
			$data['baseURL'] = base_url();
			$data['menu_1_edit_appointment'] = TRUE;
			$this->load->model('appointment','',TRUE);
			$success = $this->appointment->update_appointment_details($_POST);
			if ($success)
				$data['custom_message'] = "<div id='success'>Update successful!</div>";
			else
				$data['custom_message'] = "Update failed!";
				
			$this->load->view('custom_message',$data);
			
		} else
			redirect('/');
	}
	
	function patient_arrival() {
		$data['baseURL'] = base_url();
		$data['title'] = "Patient Arrival";
		$data['patient_arrival'] = TRUE;
		$data['menu_1_patient_arrival'] = TRUE;
		// *** THREE ADDED VARIABLES FOR THE PAGE PATIENT ARRIVAL. ADDED 17 FEB 2011
		$data['submit_url'] = "confirm_arrival";
		$data['page_title'] = "Patient Arrival";
		$data['button_value'] = "Confirm Arrival";
		// *** END OF ADDITION
		$this->load->view('patient_arrival',$data);
	}
	
	function rp_appointment() {
		if(isset($_POST['tx_mr_no'])) {
			$data['mr_no'][0] = $this->input->post('tx_mr_no');
			if ($this->input->post('tx_mr_no_couple') !== "") {
				$data['mr_no'][1] = $this->input->post('tx_mr_no_couple');
			} else if ($_POST['tx_nickname'] !== "") {
				$this->load->model('patient','',TRUE);			
				$success = $this->patient->insert($_POST);
				$data['mr_no'][1] = $success[0];
			}
			$data['baseURL'] = base_url();
			$data['title'] = "Appointment";
			$data['menu_1_returning_patient'] = TRUE;
			$data['patient_type'] = $this->input->post('patient_type');
			$this->new_appointment($data);
		} else
			redirect('/');
	}
	
	function autocomplete_patient_details() {
		$result = $this->appointment->search_patient_data($_POST['term']);
		$data['response'] = 'false';
		if (!empty($result)) {
			$data['response'] = 'true';
			$data['message'] = array();
			
			foreach($result as $row) {
				$year = "";
				$month = "";
				$day = "";
				$rt_id = "";
				$rw_id = "";
				$rt_curr = "";
				$rw_curr = "";
				if ($row->dob!="0000-00-00") list($year,$month,$day) = explode("-", $row->dob);
				if ($row->rt_rw_id!='') list($rt_id,$rw_id) = explode("/", $row->rt_rw_id);
				if ($row->rt_rw_curr!='') list($rt_curr,$rw_curr) = explode("/", $row->rt_rw_curr);
				$data['message'][] = array( 'mr_no' => $row->mr_no,
											'label' => $row->nickname,
											'salutation' => $row->salutation,
											'first_name' => $row->first_name,
											'middle_name' => $row->middle_name,
											'last_name' => $row->last_name,
											'sex' => $row->sex,
											'id_type' => $row->id_type,
											'id_no' => $row->id_no,
											'alamat_id' => $row->alamat_id,
											'rt_id' => $rt_id,
											'rw_id' => $rw_id,										
											'kelurahan_id' => $row->kelurahan_id,
											'kecamatan_id' => $row->kecamatan_id,
											'kota_id' => $row->kota_id,
											'kdpos_id' => $row->kdpos_id,
											'alamat_curr' => $row->alamat_curr,
											'rt_curr' => $rt_curr,
											'rw_curr' => $rw_curr,									
											'kelurahan_curr' => $row->kelurahan_curr,
											'kecamatan_curr' => $row->kecamatan_curr,
											'kota_curr' => $row->kota_curr,
											'kdpos_curr' => $row->kdpos_curr,
											'pob' => $row->pob,
											'dob' => $day,
											'mob' => $month,
											'yob' => $year,
											'phone_no' => $row->phone_no,
											'secondary_hp' => $row->secondary_hp,
											'home_phone' => $row->home_phone,
											'primary_email' => $row->primary_email,
											'secondary_email' => $row->secondary_email,
											'citizenship' => $row->citizenship,
											'job' => $row->job
										);
			}			
		}
		echo json_encode($data);
	}
	
	function source_autocomplete() {
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
	}
	
	function source_autocomplete_arrival() {
		$result = $this->appointment->search_patient_data_appointment($this->input->post('term'));
		$data['response'] = 'false';
		if (!empty($result)) {
			$data['response'] = 'true';
			$data['message'] = array();
			foreach($result as $row) {
				$data['message'][] = array( 'id' => $row->id,
											'label' => $row->nickname,
											'app_id' => $row->app_id,
											'nickname' => $row->nickname,
											'couple_nickname' => $row->couple_nickname,
											'couple_app_id' => $row->couple_app_id,
											'hour' => $this->appointment->get_hour_by_id($row->hour),
											'end' => $this->appointment->get_end_hour_by_id($row->end_hour),
											'doctor' => $row->doctor,
											'date' => $this->appointment->long_format_date($row->date),
											'room' => $row->room
										);
			}			
		}
		echo json_encode($data);
	}
	
	function new_patient() {
		
		$data['baseURL'] = base_url();
		if (isset($_POST['curr_date'])) {
			$data['menu_1_new_patient'] = TRUE;
			$data['title'] = 'Appointment';
			$this->load->model('patient','',TRUE);			
			$success = $this->patient->insert($_POST);// (USE $success = TRUE when testing)
			//print_r($success);exit;
			if ($success) {
				//$data['custom_message'] = "Berhasil diinput!";
				$data['patient_type'] = $_POST['patient_type'];
				$data['mr_no'] = $success;				
				$this->new_appointment($data);
			} else {
				$data['custom_message'] = "Gagal input!";
				$this->load->view('custom_message',$data);
			}
		} else {
			redirect('/control_panel');
		}
			
	}
	
	function report() {
		$data['baseURL'] = base_url();
		$data['title'] = "Report";
		$data['report_station1'] = TRUE;
		$data['no_nav'] = TRUE;
		$this->load->view('daily_report_station_1',$data);
	}
	
	function show_report($month='',$year='') {
		$body_report = "";
		if (isset($_POST['report_date'])) {
			$result = $this->appointment->daily_report($this->input->post('report_date'));
			if (!empty($result)) {
				echo "<form method='post' action='".site_url('general/export_report')."'>";
				echo "<input type='submit' value='Download Report' />";
				$title = "Daily Report for ".$this->appointment->long_format_date($this->input->post('report_date'));
				echo "<input type='hidden' name='title_report' value='".$title."'/>";
				echo "<h3>".$title."</h3>";
				echo "<table class=\"report\">";
				echo "	<tr>";
				echo "		<th>No.</th>";
				echo "		<th>MR #</th>";
				echo "		<th width=\"80px\">Name</th>";
				echo "		<th>Age</th>";
				echo "		<th>Sex</th>";
				echo "		<th>Hour</th>";
				echo "		<th>Doctor</th>";
				echo "		<th>Room</th>";
				echo "		<th>Complaint</th>";
				echo "		<th>Temp Diagnosis</th>";
				echo "		<th>Status</th>";
				echo "	</tr>";
				
				// ===============  start table report =====================
				
				$body_report .= "<table>";
				$body_report .= "	<tr>";
				$body_report .= "		<th>No.</th>";
				$body_report .= "		<th>MR #</th>";
				$body_report .= "		<th width=\"80px\">Name</th>";
				$body_report .= "		<th>Age</th>";
				$body_report .= "		<th>Sex</th>";
				$body_report .= "		<th>Hour</th>";
				$body_report .= "		<th>Doctor</th>";
				$body_report .= "		<th>Room</th>";
				$body_report .= "		<th>Complaint</th>";
				$body_report .= "		<th>Temp Diagnosis</th>";
				$body_report .= "		<th>Status</th>";
				$body_report .= "	</tr>";
				
				// ============= end header ================== //
				$i = 1;
				$class = 'odd';
				$style = "background:#f0f0f0"; // add this for report download
				
				foreach ($result as $row ) {
					echo "	<tr class=\"$class\">";
					echo "		<td>$i</td>";
					echo "		<td>$row->mr_no</td>";
					echo "		<td>".$this->appointment->get_name_by_mr($row->mr_no)."</td>";
					echo "		<td>-</td>";
					echo "		<td>".$this->appointment->get_sex_by_mr($row->mr_no)."</td>";
					echo "		<td>".$this->appointment->get_hour_by_id($row->id_hour)." - ".$this->appointment->get_end_hour_by_id($row->id_hour_end)."</td>";
					echo "		<td>".$this->appointment->get_doctor_name($row->doctor_id).($row->other_doctor_name==""?'':" ($row->other_doctor_name)")."</td>";
					echo "		<td>$row->room_id</td>";
					echo "		<td>$row->keluhan</td>";
					echo "		<td>$row->temp_diagnosis</td>";
					echo "		<td>$row->appointment_status</td>";
					echo "	</tr>";
					
					// =========== table body ============== //
					
					$body_report .= "	<tr style=\"$style\">";
					$body_report .= "		<td>$i</td>";
					$body_report .= "		<td>$row->mr_no</td>";
					$body_report .= "		<td>".$this->appointment->get_name_by_mr($row->mr_no)."</td>";
					$body_report .= "		<td>-</td>";
					$body_report .= "		<td>".$this->appointment->get_sex_by_mr($row->mr_no)."</td>";
					$body_report .= "		<td>".$this->appointment->get_hour_by_id($row->id_hour)." - ".$this->appointment->get_end_hour_by_id($row->id_hour_end)."</td>";
					$body_report .= "		<td>".$this->appointment->get_doctor_name($row->doctor_id)."</td>";
					$body_report .= "		<td>$row->room_id</td>";
					$body_report .= "		<td>$row->keluhan</td>";
					$body_report .= "		<td>$row->temp_diagnosis</td>";
					$body_report .= "		<td>$row->appointment_status</td>";
					$body_report .= "	</tr>";
					
					// add this for report download
					$style == "background:#f0f0f0" ? $style = "background:#ffffff" : $style = "background:#f0f0f0";
					// end table body =-==
					
					$i++;
					$class=='odd' ? $class='even' : $class='odd';
				}
				echo "</table>";				
				$body_report .= "</table>";
				echo "<input type='hidden' name='body_report' value='".$body_report."' />";
				echo "</form>";
				// ===============  start table report =====================
			} else {
				echo "No patient on ".$this->appointment->long_format_date($this->input->post('report_date'));
			}
		} else if ($month != '' && $year != '') {
			
			$result = $this->appointment->monthly_report($month, $year);
			if (!empty($result)) {
				echo "<form method='post' action='".site_url('general/export_report')."'>";
				echo "<input type='submit' value='Download Report' />";
				$title = "Monthly Report for ".date("F Y",mktime(0,0,0,$month,1,$year));
				echo "<input type='hidden' name='title_report' value='".$title."'/>";
				echo "<h3>".$title."</h3>";
				echo "<table class=\"report\">";
				echo "	<tr>";
				echo "		<th>No.</th>";
				echo "		<th>MR #</th>";
				echo "		<th width=\"80px\">Name</th>";
				echo "		<th>Age</th>";
				echo "		<th>Date</th>";
				echo "		<th>Sex</th>";
				echo "		<th>Hour</th>";
				echo "		<th>Doctor name</th>";
				echo "		<th>Room</th>";
				echo "		<th>Complaint</th>";
				echo "		<th>Temp Diagnosis</th>";
				echo "		<th>Status</th>";
				echo "	</tr>";
				
				// == start table header ==
				$body_report .= "<table>";
				$body_report .= "	<tr>";
				$body_report .= "		<th>No.</th>";
				$body_report .= "		<th>MR #</th>";
				$body_report .= "		<th width=\"80px\">Name</th>";
				$body_report .= "		<th>Age</th>";
				$body_report .= "		<th>Date</th>";
				$body_report .= "		<th>Sex</th>";
				$body_report .= "		<th>Hour</th>";
				$body_report .= "		<th>Doctor name</th>";
				$body_report .= "		<th>Room</th>";
				$body_report .= "		<th>Complaint</th>";
				$body_report .= "		<th>Temp Diagnosis</th>";
				$body_report .= "		<th>Status</th>";
				$body_report .= "	</tr>";
				// end table header
				
				$i = 1;
				$class = 'odd';
				$style = "background:#f0f0f0"; // add this for report download
				
				foreach ($result as $row ) {
					echo "	<tr class=\"$class\">";
					echo "		<td>$i</td>";
					echo "		<td>$row->mr_no</td>";
					echo "		<td>".$this->appointment->get_name_by_mr($row->mr_no)."</td>";
					echo "		<td>-</td>";
					echo "		<td>".$this->appointment->long_format_date($row->appointment_date)."</td>";
					echo "		<td>".$this->appointment->get_sex_by_mr($row->mr_no)."</td>";
					echo "		<td>".$this->appointment->get_hour_by_id($row->id_hour)." - ".$this->appointment->get_end_hour_by_id($row->id_hour_end)."</td>";
					echo "		<td>".$this->appointment->get_doctor_name($row->doctor_id)."</td>";
					echo "		<td>$row->room_id</td>";
					echo "		<td>$row->keluhan</td>";
					echo "		<td>$row->temp_diagnosis</td>";
					echo "		<td>$row->appointment_status</td>";
					echo "	</tr>";
					
					$body_report .= "	<tr style=\"$style\">";
					$body_report .= "		<td>$i</td>";
					$body_report .= "		<td>$row->mr_no</td>";
					$body_report .= "		<td>".$this->appointment->get_name_by_mr($row->mr_no)."</td>";
					$body_report .= "		<td>-</td>";
					$body_report .= "		<td>".($row->appointment_date)."</td>";
					$body_report .= "		<td>".$this->appointment->get_sex_by_mr($row->mr_no)."</td>";
					$body_report .= "		<td>".$this->appointment->get_hour_by_id($row->id_hour)." - ".$this->appointment->get_end_hour_by_id($row->id_hour_end)."</td>";
					$body_report .= "		<td>".$this->appointment->get_doctor_name($row->doctor_id)."</td>";
					$body_report .= "		<td>$row->room_id</td>";
					$body_report .= "		<td>$row->keluhan</td>";
					$body_report .= "		<td>$row->temp_diagnosis</td>";
					$body_report .= "		<td>$row->appointment_status</td>";
					$body_report .= "	</tr>";
					
					$i++;
					$class=='odd' ? $class='even' : $class='odd';
					// add this for report download
					$style == "background:#f0f0f0" ? $style = "background:#ffffff" : $style = "background:#f0f0f0";
				}
				echo "</table>";
				
				
				
				$body_report .= "</table>";
				echo "<input type='hidden' name='body_report' value='".$body_report."' />";
				echo "</form>";
			} else {
				echo "No patient in ".date("F Y",mktime(0,0,0,$month,1,$year));
			}
			//echo ($month." ".$year);
		} else
			redirect('/control_panel');
	}
	
	private function new_appointment($data='') {
		$data['doctor'] = $this->appointment->get_doctor_list('R');
		$data['room'] = $this->appointment->get_room_list();
		$data['hour'] = $this->appointment->get_hour_list();
		$data['nurse'] = $this->appointment->get_nurse_list();
		$data['datepicker'] = TRUE;
		//$data['form_validator'] = TRUE;
		//print_r($data);exit;
		$this->load->view('new_appointment',$data);
	}
	
	function populate_doctor($type) {
		$result = $this->appointment->get_doctor_list($type);
		if (!empty($result)) {
			foreach ($result as $row) {
				$arr[] = array(
							'optionVal' => $row->id,
							'optionText' => $row->name,
							'optionSex' => $row->sex
						);
			}
			echo json_encode($arr);
		}
	}
	
	function patient_details() {
		$data["baseURL"] = base_url();
		$data["patient_details"] = TRUE;
		$data['menu_1_patient_data'] = TRUE;
		$this->load->view("patient_detail",$data);
	}
	
	function update_patient_details() {
		if (isset($_POST['tx_nickname'])) {
			$data['baseURL'] = base_url();
			$data['menu_1_patient_data'] = TRUE;
			$this->load->model("patient",'',TRUE);
			$result = $this->patient->update_patient_details($_POST);
			if ($result) {
				if (isset($_POST['ajax']))
					echo "Success";
				else {
					
					$data['custom_message'] = "<h3>Patient Details</h3><div class='success'>Patient's data successfully saved</div>";
					$data['title'] = "Patient Details";
					$this->load->view("custom_message",$data);
				}					
			} else {
				$data['custom_message'] = "Failed";
				$data['title'] = "Patient Details";
				$this->load->view("custom_message",$data);
			}
		} else
			redirect('/');
	}

	function logout() {
		$this->session->sess_destroy();
		redirect('/welcome/index/logout');
	}
	
	function test_post() {
		print_r($_POST);exit;
	}
	
	function lagu_sion() {
		$data['baseURL'] = base_url();
		if (isset($_POST['judul_lagu'])) {
			$this->load->model('appointment','',TRUE);
			$success = $this->appointment->insert_lagu($_POST);
			if ($success)
				$data['message'] = "Success";
			else
				$data['message'] = "failed";
		}
		$this->load->view('lagu_sion',$data);
	}
}
