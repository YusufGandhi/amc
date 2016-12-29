<?php require "header.php" ?>
	<h3>Patient Details</h3>
	<div style="border:1px red;">
		<label for="tx_search">Please type in MR. no / name:</label> <input type="text" id="tx_search" />
	</div>
	<form method="post" action="<?php echo $baseURL ?>index.php/control_panel/update_patient_details">
		<table style="border:1em;">
			<tr>
				<td>MR no.</td>
				<td><span id="mr_no"></span><input type="hidden" id="tx_mr_no" name="tx_mr_no" /> <button type="button" id="edit_btn">Edit</button></td>
			</tr>
			<tr>
				<td><label for ="tx_nickname">Nickname</label></td>
				<td><input type="text" id="tx_nickname" name="tx_nickname" maxlength="30" /></td>
			</tr>
			<tr>
				<td><label for ="dd_salutation">Salutation</label></td>
				<td>
					<select id="dd_salutation" name="dd_salutation">
						<option value="">Please select</option>
						<option value="Mr.">Mr.</option>
						<option value="Mrs.">Mrs.</option>
						<option value="Ms.">Ms.</option>
					</select>
				
				</td>
			</tr>
			<tr>
				<td><label for ="tx_firstname">First name</label></td>
				<td><input type="text" id="tx_firstname" name="tx_firstname" maxlength="30" /></td>
			</tr>
			<tr>
				<td><label for ="tx_middlename">Middle name</label></td>
				<td><input type="text" id="tx_middlename" name="tx_middlename" maxlength="30" /></td>
			</tr>
			<tr>
				<td><label for ="tx_lastname">Last name</label></td>
				<td><input type="text" id="tx_lastname" name="tx_lastname" maxlength="30" /></td>
			</tr>			
			<tr>
				<td>Sex</td>
				<td><input type="radio" name="rb_sex" id="rb_sex_M" value="M" /><label for="rb_sex_M">Male</label><input type="radio" name="rb_sex" id="rb_sex_F" value="F" /><label for="rb_sex_F">Female</label></td>
			</tr>
			<tr>
				<td><label for ="dd_id_type">ID #</label></td>
				<td>
					<select id="dd_id_type" name="dd_id_type">
						<option value="">Please select</option>
						<option value="KTP">KTP</option>
						<option value="SIM">SIM</option>
						<option value="Passport">Passport</option>
					</select>
					<input type="text" id="tx_id_no" name="tx_id_no" maxlength="30" />
				</td>
			</tr>
			<tr>
				<td><label for ="tx_street_id">Address<br />(in ID card)</label></td>
				<td>
					<table>
						<tr>
							<td><label for ="tx_street_id">Alamat</label></td>
							<td><textarea id="tx_street_id" maxlength="100" name="tx_street_id" height="3" width="70"></textarea></td>
						</tr>
						<tr>
							<td><label for ="tx_rt_id">RT/RW</label></td>
							<td><input type="text" size="2" maxlength="3" id="tx_rt_id" name="tx_rt_id" />/<input type="text" size="2" maxlength="3" id="tx_rw_id" name="tx_rw_id" /></td>
						</tr>
						<tr>
							<td><label for="tx_kelurahan_id">Kelurahan</label></td>
							<td><input type="text" maxlength="30" id="tx_kelurahan_id" name="tx_kelurahan_id" /></td>
						</tr>
						<tr>
							<td><label for ="tx_kecamatan_id">Kecamatan</label></td>
							<td><input type="text" maxlength="30" id="tx_kecamatan_id" name="tx_kecamatan_id" /></td>
						</tr>
						<tr>
							<td><label for ="tx_kota_id">Kota</label></td>
							<td><input type="text" maxlength="30" id="tx_kota_id" name="tx_kota_id" /></td>
						</tr>
						<tr>
							<td><label for ="tx_kdpos_id">Kode pos</label></td>
							<td><input type="text" maxlength="5" size="5" id="tx_kdpos_id" name="tx_kdpos_id" /></td>
						</tr>
					</table>			
				</td>
			</tr>
			<tr>
				<td><label for ="tx_street_curr">Current<br />address</label></td>
				<td>
					<table>
						<tr>
							<td colspan="2"><input type="checkbox" id="ch_same" /><label for="ch_same">Same with above</label></td>							
						</tr>
						<tr>
							<td><label for ="tx_street_curr">Alamat</label></td>
							<td><textarea id="tx_street_curr" name="tx_street_curr" maxlength="100" height="3" width="70"></textarea></td>
						</tr>
						<tr>
							<td><label for ="tx_rt_curr">RT/RW</label></td>
							<td><input type="text" size="2" maxlength="3" id="tx_rt_curr" name="tx_rt_curr" />/<input type="text" size="2" maxlength="3" id="tx_rw_curr" name="tx_rw_curr" /></td>
						</tr>
						<tr>
							<td><label for ="tx_kelurahan_curr">Kelurahan</label></td>
							<td><input type="text" maxlength="30" id="tx_kelurahan_curr" name="tx_kelurahan_curr" /></td>
						</tr>
						<tr>
							<td><label for ="tx_kecamatan_curr">Kecamatan</label></td>
							<td><input type="text" maxlength="30" id="tx_kecamatan_curr" name="tx_kecamatan_curr" /></td>
						</tr>
						<tr>
							<td><label for ="tx_kota_curr">Kota</label></td>
							<td><input type="text" maxlength="30" id="tx_kota_curr" name="tx_kota_curr" /></td>
						</tr>
						<tr>
							<td><label for ="tx_kdpos_curr">Kode pos</label></td>
							<td><input type="text" maxlength="5" size="5" id="tx_kdpos_curr" name="tx_kdpos_curr" /></td>
						</tr>
					</table>			
				</td>
			</tr>
			<tr>
				<td><label for ="tx_pob">Place / Date<br />of birth</label></td>
				<td>
					<input type="text" id="tx_pob" size="30" name="tx_pob" /> / 
						<select id="dd_mob" name="dd_mob">
							<option value="">Month</option>
					<?php
						for ($i = 1; $i<=12; $i++)
							printf("\t<option value=\"%02d\">".date("F", mktime(0,0,0,$i,1,2010))."</option>\n",$i);
					?>
					</select>
					<select id="dd_dob" name="dd_dob">
						<option value="">Day</option>
					<?php
						for ($i = 1; $i<=31; $i++) 
							printf("\t<option value=\"%02d\">%d</option>\n",$i,$i);
					?>
					</select>				
					<select id="dd_yob" name="dd_yob">
						<option value="">Year</option>
					<?php
						$n = date("Y");
						for ($i = $n-120; $i<=$n; $i++)
							echo "\t<option value=\"$i\">$i</option>\n";
					?>
					</select>
				
				</td>
			</tr>
			<tr>
				<td><label for="tx_primary_hp">Primary HP no.</label></td>
				<td><input type="text" maxlength="20" id="tx_primary_hp" name="tx_primary_hp" /></td>
			</tr>
			<tr>
				<td><label for="tx_secondary_hp">Secondary HP no.</label></td>
				<td><input type="text" maxlength="30" id="tx_secondary_hp" name="tx_secondary_hp" /></td>
			</tr>
			<tr>
				<td><label for="tx_home_phone">Home Phone no.</label></td>
				<td><input type="text" maxlength="20" id="tx_home_phone" name="tx_home_phone" /></td>
			</tr>
			<tr>
				<td><label for="tx_email_1">Primary e-mail</label></td>
				<td><input type="text" maxlength="100" id="tx_email_1" name="tx_email_1" /></td>
			</tr>
			<tr>
				<td><label for="tx_email_2">Secondary e-mail</label></td>
				<td><input type="text" maxlength="100" id="tx_email_2" name="tx_email_2" /></td>
			</tr>
			<tr>
				<td><label for="dd_citizenship">Citizenship</label></td>
				<td>
					<select id="dd_citizenship" name="dd_citizenship">
						<option value="">Please select</option>
						<option value="WNI">WNI</option>
						<option value="WNA">WNA</option>
					</select>
				</td>
			</tr>
			<tr>
				<td><label for="tx_job">Job</label></td>
				<td><input type="text" maxlength="30" id="tx_job" name="tx_job" /></td>
			</tr>
			<tr>
				<td colspan="2">
					<button type="submit" id="save_btn">Save Details</button>
					<button type="button" id="cancel_btn">Cancel Editing</button>
				</td>
			</tr>
		</table>
	</form>

<?php require "footer.php" ?>