<?php require "header.php" ?>
		<h3>Appointment Cancellation</h3>
		<form method="post" action="<?php echo $target_url ?>">
		<p class="ui-widget"><label for="search_patient_arrival">Search</label> <input type="text" id="search_patient_arrival" /></p>		
		<div id="patient_data">			
			<input type="hidden" name="id_appointment" id="id_appointment" />
			<table>
				<tr>
					<td colspan='3'><h4>Appointment Data</h4></td>
				</tr>
				<tr>
					<td>Name</td>
					<td>:</td>
					<td><span id="name_patient"></span><span id="couple_nickname"></span></td>
				</tr>
				<tr>
					<td>Date / Hour</td>
					<td>:</td>
					<td><span id="date_hour_appointment"></span></td>
				</tr>
				<tr>
					<td>Doctor</td>
					<td>:</td>
					<td><span id="doctor"></span></td>
				</tr>
				<tr>
					<td>Room</td>
					<td>:</td>
					<td><span id="room_no"></span></td>
				</tr>
				<tr>
					<td colspan='3'><input type="submit" value="Cancel Appointment" /> <input type="reset" value="Reset" id="bt_reset" />
				</tr>
			</table>
		</div>
		</form>
<?php require "footer.php" ?>