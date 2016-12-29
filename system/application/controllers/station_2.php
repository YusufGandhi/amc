<?php
/**
 * @property CI_Loader $load
 * @property CI_Form_validation $form_validation
 * @property CI_Input $input
 * @property CI_Email $email
 * @property CI_DB_active_record $db
 * @property CI_DB_forge $dbforge
 */

class Station_2 extends Controller {

	function Station_2()
	{
		parent::Controller();
		$this->load->helper('url');
		$this->load->helper('form');
		$this->load->library('session');		
		if ($this->session->userdata('logged_in') != TRUE || $this->session->userdata('station') != '2') {
				redirect('/welcome');
		}
		//$this->load->model('appointment','',TRUE);
		
	}
	
	function index()
	{
		
		$data['baseURL'] = base_url();
		$data['front_logo'] = "exam.jpg";
		$data['title'] = "Control Panel Station ".$this->session->userdata('station')." - ".$this->session->userdata('name');
		$data['custom_message'] = "<h2>Welcome to Control Panel Station ".$this->session->userdata('station')."</h2><br />Please select the operation you need in the menu to your left.";		
		$this->load->view('custom_message',$data);

	}
	
	function history() {
		$data['search'] = TRUE;
		$data['search_initial'] = TRUE;
		$data['baseURL'] = base_url();
		$data['menu_2_history'] = TRUE;
		$this->load->view('station_2_search_by_mr',$data);
	}
	
	function result() {
		if (isset($_POST['tx_mr_no'])) {			
			$this->load->model('appointment','',TRUE);
			$this->load->model('patient','',TRUE);
			$data['atts'] = array(
                                              'width'      => '600',
                                              'height'     => '600',
                                              'scrollbars' => 'yes',
                                              'status'     => 'no',
                                              'resizable'  => 'no',
                                              'screenx'    => '0',
                                              'screeny'    => '0'
                                            );
			$data['search'] = TRUE;
			$data['data_visit'] = $this->appointment->get_appointment_by_mr($_POST['tx_mr_no']);
			$data['patient_details'] = $this->patient->get_patient_details_by_mr($_POST['tx_mr_no']);
			$data['mr_no'] = $_POST['tx_mr_no'];
			$data['baseURL'] = base_url();
			$data['menu_2_history'] = TRUE;
			$this->load->view('station_2_search_by_mr',$data);
		} else
			redirect('/');
	}
	
			
	function test_post() {
		print_r($_POST);exit;
	}
	
	function patient_list() {
		$this->load->model('payment','',TRUE);
		$data['baseURL'] = base_url();
		$data['menu_2_exam'] = TRUE;
		// FETCH THE DATA OF PATIENT NOT YET EXAMINED
		// PARAMETER USING 'N' FOR THE FUNCTION GET PATIENT EXAMINED
		// UPDATED JANUARY 12, 2011: FETCH BASED ON DOCTOR ID
		$data['result'] = $this->payment->get_patient_examined('N',$this->session->userdata('id_doctor'));//print_r($data);exit;
		$this->load->view('station_2_patient_list',$data);
	}
	
	function save_edit() {
		if (isset($_POST['sistole'])) {
			$this->load->model('appointment','',TRUE);
			$update = $this->appointment->update_appointment_visit_detail($_POST);
			//echo $update;exit;
			if ($update) {
				$this->visit_details($_POST['app_id'],$_POST['visit_count'],TRUE);
			} else
				$this->visit_details($_POST['app_id'],$_POST['visit_count'],FALSE);
		} else
			redirect('/');

	}
	
	function exam($app_id) {
		$this->load->model('payment','',TRUE);
		$this->load->model('appointment','',TRUE);
		$this->load->model('patient','',TRUE);
		$data['patient'] = $this->patient->get_patient_details($app_id);
		$data['patient']->appointment_date = $this->long_format_date($data['patient']->appointment_date);
		$data['couple_app_id'] = $this->appointment->check_couple_appointment_id_by_app($app_id);
		$data['visit'] = $this->patient->count_visit($data['patient']->mr_no);
		$data['history'] = $this->patient->visit_history($data['patient']->mr_no, $app_id);
		$data['room'] = $this->appointment->get_room_list();
		$data['hour'] = $this->appointment->get_hour_list();
		$data['nurse_list'] = $this->appointment->get_nurse_list();
		$data['nurse'] = $this->appointment->get_nurse_name_by_app($app_id);
		$data['doctor'] = $this->appointment->get_doctor_by_app($app_id);
		$data['exam'] = TRUE;
		$data['no_nav'] = TRUE;
		$data['curr_year'] = date("Y");
		$data['app_id'] = $app_id;
		$data['baseURL'] = base_url();
		$data['title'] = "Examination";
		$data['payment_station5'] = TRUE;
		$data['tindakan'] = $this->payment->get_tindakan();
		$data['specimen'] = $this->payment->get_specimen();
		$data['lab'] = $this->payment->get_lab();
		$data['obat'] = $this->payment->get_obat();
		$result = $this->appointment->get_temporary_diagnosis($app_id);
		$data['keluhan'] = $result->keluhan;
		$data['temp_diagnosis'] = $result->temp_diagnosis;
		$data['form_validator'] = TRUE;
		$data['auto_grow'] = TRUE;
		$data['digit_group'] = TRUE;
		
		//print_r($data);exit;
		$this->load->view('station_2_exam',$data);
	}
	
	function visit_details($app_id,$i,$update_success='') {
		$this->load->model('medical','',TRUE);
		$this->load->model('patient','',TRUE);
		$this->load->model('appointment','',TRUE);
		$data['update_success'] = $update_success;
		$data['patient'] = $this->patient->get_patient_details($app_id);
		$data['patient']->appointment_date = $this->long_format_date($data['patient']->appointment_date);
		$data['app_id'] = $app_id;
		$data['baseURL'] = base_url();
		$data['no_nav'] = TRUE;
		$data['form_validator'] = TRUE;
		$data['visit_count'] = $i;		
		$data['nurse'] = $this->appointment->get_nurse_name_by_app($app_id);
		$data['curr_year'] = date("Y");
		$data['no'] = $i; // visit ke berapa
		$data['doctor'] = $this->appointment->get_doctor_by_app($app_id);
		//$data['baseURL'] = base_url();
		$data['details'] = $this->medical->previous($app_id);
		$data['tindakan'] = $this->medical->detil_tindakan($app_id);
		$data['lab'] = $this->medical->detil_lab($app_id);
		$data['obat'] = $this->medical->detil_obat($app_id);
		$this->load->view('station_2_previous_visit',$data);
		//print_r($details);exit;
	}
	
	function save_exam() {
		//print_r($_POST);exit;
		$this->load->model('payment','',TRUE);
		if (isset($_POST['next_appointment']) && $_POST['next_appointment'] == "Y") {
			$this->load->model('appointment','',TRUE);
			$app_id = $this->appointment->insert_appointment($_POST);
			if ($app_id) {
				$this->payment->new_payment($app_id[0], $_POST['doc_type'], $_POST['patient_type']);
			}
		}
		$insert = $this->payment->save_payment_details($_POST);
		if ($insert)			
			$data['custom_message'] = "<div class='success'>Successfully saved the details</div>";
		else
			$data['custom_message'] = "Failed. Please press BACK and re-save";
		$data['baseURL'] = base_url();
		$data['menu_2_exam'] = TRUE;
		$this->load->view('custom_message',$data);
		
	}
	
	function report() {
		//$this->load->model();
		
	}
	
	function generate_doctor_schedule() {	
		$this->load->model('appointment','',TRUE);
		$doctor = $this->appointment->get_doctor_list();
		$room = $this->appointment->get_room_list();
		$hour = $this->appointment->get_hour_list();

		echo "<h3>Schedule on ". $this->appointment->long_format_date($_POST['date']) ."</span></h3>";
		//echo "<!-- THIS IS THE TABLE TO FILL IN THE SCHEDULE OF HOUR, ROOM AVAILABILITY -->";
		echo "<table class=\"report\" >"; //previous class: class=\"tb_schedule\"
		
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
	
	function source_paramita() 
	{
		if (isset($_POST['term'])) {	
			$this->load->model('medical','',TRUE);
			$result = $this->medical->search_paramita($_POST['term']);
			$data['response'] = 'false';
			if (!empty($result)) {
				$data['response'] = 'true';
				$data['message'] = array();
				foreach($result as $row) {
					$data['message'][] = array( 'id' => $row->id,
												'label' => $row->pemeriksaan,
												'harga' => $row->harga,
												'klasifikasi' => $row->klasifikasi
											);
				}			
			}
			echo json_encode($data);
		} else
			redirect('/');
	}
	
	private function long_format_date($date) {
		list($year,$month,$day) = explode("-",$date);
		return date("F j, Y",mktime(0,0,0,$month,$day,$year));
	}


}
