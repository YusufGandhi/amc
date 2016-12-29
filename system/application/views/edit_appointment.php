<?php require "header.php" ?>

	<form method="post" action="<?php echo $baseURL // ?>index.php/control_panel/save_update">
		<div id="schedule" style="float:right;width:400px;text-align:center"></div>
		Appointment Details for Patient <strong><?php echo $appointment->mr_no ?></strong>
		<?php 
		if (isset($couple_mr_no)) {
			echo " & <strong>$couple_mr_no</strong>"; 
			echo "<input type='hidden' name='couple_app_no' id='couple_app_no' value='$appointment->couple_appointment_id' />";
		}
		?>
				
		<input type='hidden' name='app_id' id='app_id' value='<?php echo $appointment->appointment_number ?>' />
		<input type="hidden" id="date" name="date" value="<?php echo $appointment->appointment_date;  ?>" />
		
		<table>	
			<tr>
				<td>Doctor</td>
				<td>
					<!-- Doctor's list goes here -->
					<input type="radio" name="doc_type" id="rb1" value="R"<?php echo ($appointment->type=="R"?" checked='checked'":"") ?> /><label for="rb1">GP</label>
					<input type="radio" name="doc_type" id="rb2" value="S"<?php echo ($appointment->type=="S"?" checked='checked'":"") ?> /><label for="rb2">Specialists</label><br />
					<select name="dd_list_doctor" id="dd_list_doctor" width="200">
					<?php
						echo "<option value='0'>Select Doctor</option>";
						if (!empty($doctor)) {
							foreach ($doctor as $row) {
								echo "<option value='$row->id'".($row->id == $appointment->doctor_id?" selected='selected'":"").">$row->name ($row->sex)</option>";
							}
						}
					?>
					</select><br />
					<div id="other_doctor" class="hide"><label class="absolute_left" for="other_name">Name of the doctor</label><input type="text" name="other_name" id="other_name" value="<?php echo $appointment->other_doctor_name ?>" /></div>
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
								echo "<option value='$row->id'".($row->id == $appointment->nurse_id?" selected='selected'":"").">$row->name</option>";
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
							echo "<option value='$row->id'".($row->id == $appointment->id_hour?" selected='selected'":"").">$row->hour</option>";
						}
					?>
					</select>
					to
					<select name="dd_list_hour_end" id="dd_list_hour_end">
						<option value='0'>Select time</option>
					<?php
						foreach ($hour as $row) {
							echo "<option value='$row->id'".($row->id == $appointment->id_hour_end?" selected='selected'":"").">$row->end</option>";
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
							echo "<option value='$row->id'".($row->id == $appointment->room_id?" selected='selected'":"").">$row->room_number</option>";
						}
					?>
					</select>						
				</td>
			</tr>							
			<tr>
				<td colspan='2'>
					Keluhan:<br />
					<textarea name="tx_keluhan" cols="35" rows="5" maxlength="1000" wrap='hard'><?php echo $appointment->keluhan ?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					Current diagnosis:<br />
					<textarea name="tx_temp_diagnosis" cols="35" rows="5" maxlength="1000" wrap='hard'><?php echo $appointment->temp_diagnosis ?></textarea>
				</td>
			</tr>
		</table>
		<input type="submit" value="Update Appointment" /> <input type="reset" value="Reset" id="reset_appointment" />
	</form>
<?php require "footer.php" ?>