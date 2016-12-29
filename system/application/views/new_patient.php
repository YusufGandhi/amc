<?php require "header.php" ?>
	<h3>Hotline New Patient Registration</h3>
	
	<form id="form_new_patient" method="post" action="<?php echo $baseURL ?>index.php/control_panel/new_patient"><!--     -->
	<input type="hidden" name="curr_date" value="<?php echo date("Y-m-d") ?>" />
	<input type="hidden" name="patient_type" id="patient_type" value="NP" />
	<!--<input type="hidden" name="mr_no" value="<?php echo $mr_no ?>" />
	MR #: <strong><?php echo $mr_no ?></strong>-->
	<div>
		<table id="tb_form">
			<tr>
				<td><label for="tx_nickname">Nickname <span class="required">*</span></label></td>
				<td> <input type="text" id="tx_nickname" name="tx_nickname" class="required" /></td>
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
				<td><input type="text" name="tx_phone" id="tx_phone" class="required" /></td>
			</tr>
		</table>
		<input type="checkbox" id="with_couple" name="with_couple" value /><label for="with_couple">With Couple</label>
	</div>
	<div id="couple">
		<h5>Couple Details:</h5>
		<table>
			<tr>
				<td><label for="tx_couple_nickname">Nickname <span class="required">*</span></label></td>
				<td> <input type="text" id="tx_couple_nickname" name="tx_couple_nickname" /></td>
			</tr>
			<tr>
				<td>Sex</td>
				<td>
					<input type="radio" name="rb_couple_sex" id="sex_couple_1" value="M" checked="checked" /><label for="sex_couple_1">Male</label>
					<input type="radio" name="rb_couple_sex" id="sex_couple_2" value="F" /><label for="sex_couple_2">Female</label>
				</td>
			</tr>
			<tr>
				<td><label for="tx_couple_phone">Phone # <span class="required">*</span><label></td>
				<td><input type="text" name="tx_couple_phone" id="tx_couple_phone" /></td>
			</tr>			
		</table>
	</div>
	<input type="submit" value="Register" /> <input type="reset" value="Clear" />
	</form>
<?php require "footer.php" ?>