<?php require "header.php" ?>
		<h3>Returning Patient Appointment</h3>
		<form id='rp_form' method="post" action="<?php echo site_url('/control_panel/rp_appointment') //rp_appointment ?>">
		<input type="hidden" name="patient_type" id="patient_type" value="RP" />
		<p class="ui-widget"><label for="search_patient">Search</label> <input type="text" id="search_patient" /></p>		
		<div id="patient_data">
			<input type="hidden" name="tx_mr_no" id="tx_mr_no" class="required main-data" />
			<table>
				<tr>
					<td colspan='2'><h3>Patient Data</h3></td>
				</tr>
				<tr>
					<td>Medical Record No : </td>
					<td><span id="mr_no"></span></td>
				</tr>
				<tr>
					<td colspan='2'><div id="couple_check" style='display:none'><input type="checkbox" name="with_couple_check" id="with_couple" /><label for="with_couple">With Couple</label></div></td>
				</tr>
			</table>
			
		</div>
		<div id="new_patient_couple" style="display:none">
			<h5>Couple Details:</h5>
			<table id="tb_form">
				<tr>
					<td><label for="tx_nickname">Nickname <span class="required">*</span></label></td>
					<td> <input type="text" id="tx_nickname" name="tx_nickname" /></td>
				</tr>
				<tr>
					<td>Sex</td>
					<td>
						<input type="radio" name="rb_sex" id="sex1" value="M" checked="checked" /><label for="sex1">Male</label>
						<input type="radio" name="rb_sex" id="sex2" value="F" /><label for="sex2">Female</label>
					</td>
				</tr>
				<tr>
					<td><label for="tx_phone">Phone # <span class="required">*</span><label></td>
					<td><input type="text" name="tx_phone" id="tx_phone" /></td>
				</tr>
			</table>
		</div>
		<div id="old_patient_couple" style='display:none'>
			<h5>Couple Details:</h5>
			<input type='hidden' id='tx_mr_no_couple' name='tx_mr_no_couple' />
			<p class="ui-widget"><label for="search_patient_couple">Search</label> <input type="text" id="search_patient_couple" /></p>
			Medical Record No : <span id='couple_mr_no'></span>
		</div>
		<input type="submit" value="Make appointment" /> <input type="reset" value="Reset" id="bt_reset" />
		</form>
		<div id='dialog' title='Pilih Tipe Pasien Pasangan'>
			Apakah pasangan adalah Pasien Lama atau Pasien Baru?
		</div>
<?php require "footer.php" ?>