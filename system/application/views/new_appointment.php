<?php require "header.php" ?>

	<form id='new_appointment_form' method="post" action="<?php echo $baseURL ?>index.php/control_panel/save_appointment">
	<input type="hidden" id="date" name="date" value="<?php echo date("Y-m-d"); ?>" />
		<div id="schedule" style="float:right;width:400px;text-align:center"></div>
		<!-- DIRECT IMPACT ON CHANGING THE RETURN $mr_no -->
		Appointment Details for Patient <strong><?php echo isset($mr_no[1]) ? "<br />".$mr_no[0]."</strong> & <strong>".$mr_no[1] : $mr_no[0] ?></strong>
		
		<input type='hidden' value='<?php echo $mr_no[0]?>' name='mr_no' />
		<?php 
			if(isset($mr_no[1])) {
				echo "<input type='hidden' value='$mr_no[1]' name='mr_no1' />";
			}		
		?>
		
		
		<!-- END OF DIRECT IMPACT -->
		<input type="hidden" value="<?php echo $patient_type ?>" name="patient_type" />
		<br /><!--<input type='checkbox' name='med_only' id='med_only' /><label for='med_only'>Taking medicine only</label>-->
		<table id='main-table'>	
			<tr>
				<td>Doctor</td>
				<td>
					<!-- Doctor's list goes here -->
					<input type="radio" name="doc_type" id="rb1" value="R" checked="checked" /><label for="rb1">GP</label>
					<input type="radio" name="doc_type" id="rb2" value="S" /><label for="rb2">Specialists</label><br />
					<select name="dd_list_doctor" id="dd_list_doctor" width="200">
					<?php
						echo "<option value='0'>Select Doctor</option>";
						if (!empty($doctor)) {
							foreach ($doctor as $row) {
								echo "<option value='$row->id'>$row->name ($row->sex)</option>";
							}
						}
					?>
					</select><br />
					<div id="other_doctor" class="hide"><label class="absolute_left" for="other_name">Name of the doctor</label><input type="text" name="other_name" id="other_name" /></div>
				</td>
			</tr>
			<tr>
				<td>Nurse</td>
				<td>
					<select name="dd_list_nurse" id="dd_list_nurse">
					<?php 
						echo "<option value='0'>Select Nurse</option>";
						if (!empty($nurse)) {
							foreach ($nurse as $row)
								echo "<option value='$row->id'>$row->name</option>";
						}
					?>
					</select>
				</td>
			</tr>
			<tr align='center'>
				<td colspan='2'><div id="datepicker"></div></td>				
			</tr>
			<tr>
				<td>Time</td>
				<td>
					From
					<select name="dd_list_hour_start" id="dd_list_hour_start">
						<option value='0'>Select time</option>
					<?php
						foreach ($hour as $row) {
							echo "<option value='$row->id'>$row->hour</option>";
						}
					?>
					</select>
					to
					<select name="dd_list_hour_end" id="dd_list_hour_end">
						<option value='0'>Select time</option>
					<?php
						foreach ($hour as $row) {
							echo "<option value='$row->id'>$row->end</option>";
						}
					?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Room</td>
				<td>
					<select name="dd_list_room" id="dd_list_room">
					<?php
						foreach ($room as $row) {
							echo "<option value='$row->id'>$row->room_number</option>";
						}
					?>
					</select>						
				</td>
			</tr>							
			<tr>
				<td colspan='2'>
					Keluhan:<br />
					<textarea name="tx_keluhan" cols="35" rows="5" maxlength="1000"></textarea>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					Current diagnosis:<br />
					<textarea name="tx_temp_diagnosis" cols="35" rows="5" maxlength="1000"></textarea>
				</td>
			</tr>
		</table>
		<br />
		<input type='checkbox' value='Y' id='free_admin' name='free_admin' /><label for='free_admin'><strong>GIVE FREE ADMINISTRATION FEE!</strong></label>
		<br /><br /><input type="submit" value="Create Appointment" /> <input type="reset" value="Reset" id="reset_appointment" />
	</form>
<?php require "footer.php" ?>